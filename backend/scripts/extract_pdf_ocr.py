#!/usr/bin/env python3
"""
Script para extrair texto de PDFs escaneados usando OCR (Tesseract)
Ideal para arquivos PDF que são imagens digitalizadas
"""

import os
import sys
import subprocess
from pathlib import Path

def extract_text_from_pdf(pdf_path, output_path=None, language='ita'):
    """
    Extrai texto de um PDF escaneado usando OCR
    
    Args:
        pdf_path: Caminho para o arquivo PDF
        output_path: Caminho para salvar o texto extraído (opcional)
        language: Idioma para OCR (padrão: 'ita' para italiano)
    
    Returns:
        Texto extraído do PDF
    """
    pdf_path = Path(pdf_path)
    
    if not pdf_path.exists():
        print(f"❌ Erro: Arquivo não encontrado: {pdf_path}")
        sys.exit(1)
    
    # Criar diretório temporário para imagens
    temp_dir = pdf_path.parent / 'temp_ocr'
    temp_dir.mkdir(exist_ok=True)
    
    print(f"📄 Processando: {pdf_path.name}")
    print(f"🌍 Idioma OCR: {language}")
    print(f"📁 Diretório temporário: {temp_dir}")
    
    try:
        # Passo 1: Converter PDF para imagens (uma por página)
        print("\n🔄 Passo 1/3: Convertendo PDF para imagens...")
        images_prefix = temp_dir / 'page'
        cmd_convert = [
            'pdftoppm',
            '-png',
            '-r', '300',  # DPI (maior = melhor qualidade)
            str(pdf_path),
            str(images_prefix)
        ]
        subprocess.run(cmd_convert, check=True, capture_output=True)
        
        # Listar imagens geradas
        image_files = sorted(temp_dir.glob('page-*.png'))
        total_pages = len(image_files)
        print(f"✅ {total_pages} páginas convertidas para imagens")
        
        # Passo 2: Executar OCR em cada imagem
        print(f"\n🔍 Passo 2/3: Executando OCR em {total_pages} páginas...")
        all_text = []
        
        for i, image_file in enumerate(image_files, 1):
            print(f"  📖 Processando página {i}/{total_pages}...", end=' ')
            
            # Executar tesseract
            cmd_ocr = [
                'tesseract',
                str(image_file),
                'stdout',
                '-l', language,
                '--psm', '3',  # Page segmentation mode: Fully automatic
                '--oem', '3'   # OCR Engine mode: Default (best available)
            ]
            
            result = subprocess.run(cmd_ocr, capture_output=True, text=True)
            page_text = result.stdout.strip()
            
            if page_text:
                all_text.append(f"\n{'='*80}\n")
                all_text.append(f"PÁGINA {i}\n")
                all_text.append(f"{'='*80}\n\n")
                all_text.append(page_text)
                print(f"✅ ({len(page_text)} caracteres)")
            else:
                print("⚠️  (texto vazio)")
        
        # Passo 3: Salvar resultado
        combined_text = '\n'.join(all_text)
        
        if output_path:
            output_path = Path(output_path)
        else:
            output_path = pdf_path.with_suffix('.txt')
        
        print(f"\n💾 Passo 3/3: Salvando texto extraído...")
        output_path.write_text(combined_text, encoding='utf-8')
        
        print(f"✅ Texto salvo em: {output_path}")
        print(f"📊 Total de caracteres: {len(combined_text):,}")
        print(f"📊 Total de linhas: {len(combined_text.splitlines()):,}")
        
        # Limpar arquivos temporários
        print(f"\n🧹 Limpando arquivos temporários...")
        for img in image_files:
            img.unlink()
        temp_dir.rmdir()
        
        return combined_text
        
    except subprocess.CalledProcessError as e:
        print(f"❌ Erro ao executar comando: {e}")
        print(f"Saída: {e.output if hasattr(e, 'output') else 'N/A'}")
        sys.exit(1)
    except Exception as e:
        print(f"❌ Erro inesperado: {e}")
        sys.exit(1)

def main():
    if len(sys.argv) < 2:
        print("Uso: python3 extract_pdf_ocr.py <arquivo.pdf> [saida.txt] [idioma]")
        print("\nExemplo:")
        print("  python3 extract_pdf_ocr.py ItalB1-25.pdf")
        print("  python3 extract_pdf_ocr.py ItalB1-25.pdf saida.txt ita")
        print("\nIdiomas suportados:")
        print("  ita = Italiano")
        print("  eng = Inglês")
        print("  por = Português")
        sys.exit(1)
    
    pdf_path = sys.argv[1]
    output_path = sys.argv[2] if len(sys.argv) > 2 else None
    language = sys.argv[3] if len(sys.argv) > 3 else 'ita'
    
    print("\n" + "="*80)
    print("🔍 EXTRAÇÃO DE TEXTO COM OCR (Tesseract)")
    print("="*80 + "\n")
    
    text = extract_text_from_pdf(pdf_path, output_path, language)
    
    # Mostrar preview dos primeiros 500 caracteres
    print("\n" + "="*80)
    print("📝 PREVIEW DO TEXTO EXTRAÍDO:")
    print("="*80 + "\n")
    print(text[:500] + "..." if len(text) > 500 else text)
    print("\n✅ Extração concluída com sucesso!")

if __name__ == '__main__':
    main()
