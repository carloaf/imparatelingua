<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\PdfToText\Pdf;
use thiagoalessio\TesseractOCR\TesseractOCR;

class ExtractPdfText extends Command
{
    protected $signature = 'pdf:extract {filename} {--ocr : Use OCR for image-based PDFs}';
    
    protected $description = 'Extract text from PDF file in storage/app/imports';

    public function handle()
    {
        $filename = $this->argument('filename');
        $useOcr = $this->option('ocr');
        $filepath = storage_path('app/imports/' . $filename);

        if (!file_exists($filepath)) {
            $this->error("❌ Arquivo não encontrado: {$filename}");
            return 1;
        }

        $this->info("📄 Extraindo texto de: {$filename}");
        $this->info("📊 Tamanho do arquivo: " . $this->formatBytes(filesize($filepath)));

        try {
            if ($useOcr) {
                $this->info("🔍 Usando OCR (Tesseract) para extração...");
                $text = $this->extractWithOcr($filepath);
            } else {
                $this->info("📝 Tentando extração direta do PDF...");
                $text = $this->extractDirectly($filepath);
                
                // Se o texto extraído for muito curto, o PDF provavelmente é baseado em imagem
                if (strlen(trim($text)) < 100) {
                    $this->warn("⚠️  Pouco texto extraído. O PDF pode ser baseado em imagens.");
                    $this->info("💡 Tente novamente com a opção --ocr");
                    
                    if ($this->confirm('Deseja tentar com OCR agora?', true)) {
                        $text = $this->extractWithOcr($filepath);
                    }
                }
            }

            // Salvar texto extraído
            $outputFile = storage_path('app/imports/' . pathinfo($filename, PATHINFO_FILENAME) . '_extracted.txt');
            file_put_contents($outputFile, $text);
            
            $this->newLine();
            $this->info("✅ Texto extraído com sucesso!");
            $this->info("📝 Total de caracteres: " . strlen($text));
            $this->info("📄 Arquivo salvo em: " . basename($outputFile));
            
            // Mostrar preview
            $this->newLine();
            $this->line("📖 Preview (primeiras 500 caracteres):");
            $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");
            $this->line(substr($text, 0, 500) . "...");
            $this->line("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━");

            return 0;

        } catch (\Exception $e) {
            $this->error("❌ Erro ao extrair texto: " . $e->getMessage());
            return 1;
        }
    }

    private function extractDirectly($filepath)
    {
        // Tentar extrair texto diretamente do PDF com pdftotext
        try {
            $text = Pdf::getText($filepath);
            return $text;
        } catch (\Exception $e) {
            $this->warn("⚠️  Extração direta falhou: " . $e->getMessage());
            throw $e;
        }
    }

    private function extractWithOcr($filepath)
    {
        $this->info("🖼️  Convertendo PDF para imagens...");
        
        // Criar diretório temporário
        $tempDir = storage_path('app/temp/pdf_images');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        // Converter PDF para imagens usando pdftoppm (do poppler-utils)
        $outputPrefix = $tempDir . '/page';
        $command = sprintf(
            'pdftoppm -png "%s" "%s" 2>&1',
            $filepath,
            $outputPrefix
        );
        
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new \Exception("Erro ao converter PDF para imagens: " . implode("\n", $output));
        }

        // Encontrar todas as imagens geradas
        $images = glob($tempDir . '/page-*.png');
        
        if (empty($images)) {
            throw new \Exception("Nenhuma imagem foi gerada do PDF");
        }

        $this->info("📄 Total de páginas: " . count($images));
        
        // Processar cada página com OCR
        $allText = [];
        $bar = $this->output->createProgressBar(count($images));
        $bar->start();

        foreach ($images as $imagePath) {
            try {
                $ocr = new TesseractOCR($imagePath);
                $ocr->lang('ita', 'por'); // Italiano e Português
                $pageText = $ocr->run();
                $allText[] = $pageText;
                $bar->advance();
            } catch (\Exception $e) {
                $this->warn("\n⚠️  Erro ao processar " . basename($imagePath) . ": " . $e->getMessage());
            }
        }

        $bar->finish();
        $this->newLine();

        // Limpar imagens temporárias
        foreach ($images as $imagePath) {
            @unlink($imagePath);
        }
        @rmdir($tempDir);

        return implode("\n\n=== PÁGINA SEGUINTE ===\n\n", $allText);
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
