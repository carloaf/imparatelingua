<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CilsCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Ascolto',
                'slug' => 'ascolto',
                'description' => 'Compreensão auditiva - Avalia a capacidade de compreender textos orais em diferentes contextos'
            ],
            [
                'name' => 'Comprensione della Lettura',
                'slug' => 'comprensione-lettura',
                'description' => 'Compreensão de leitura - Avalia a capacidade de compreender textos escritos de diversos tipos'
            ],
            [
                'name' => 'Analisi delle Strutture di Comunicazione',
                'slug' => 'analisi-strutture',
                'description' => 'Análise das estruturas de comunicação - Avalia conhecimentos gramaticais e lexicais'
            ],
            [
                'name' => 'Produzione Scritta',
                'slug' => 'produzione-scritta',
                'description' => 'Produção escrita - Avalia a capacidade de produzir textos escritos adequados ao contexto'
            ],
            [
                'name' => 'Produzione Orale',
                'slug' => 'produzione-orale',
                'description' => 'Produção oral - Avalia a capacidade de se expressar oralmente em diferentes situações'
            ]
        ];

        foreach ($categories as $category) {
            \App\Models\Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }

        echo "✅ Categorias CILS oficiais criadas/atualizadas com sucesso!\n";
    }
}
