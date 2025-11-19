<template>
  <div class="home-view">
    <!-- Hero Section -->
    <div class="hero bg-gradient-to-r from-blue-500 to-purple-600 text-white py-16 mb-8">
      <div class="container mx-auto px-4 text-center">
        <h1 class="text-5xl font-bold mb-4">🇮🇹 ImparaLingua</h1>
        <p class="text-xl mb-8">Aprenda Italiano com Exercícios CILS</p>
        <router-link
          to="/exams"
          class="bg-white text-blue-600 px-8 py-3 rounded-full font-bold hover:bg-gray-100 transition-colors inline-block"
        >
          Começar Agora
        </router-link>
      </div>
    </div>

    <!-- Features -->
    <div class="container mx-auto px-4 mb-12">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="feature-card bg-white rounded-lg shadow-lg p-6 text-center">
          <div class="text-5xl mb-4">📚</div>
          <h3 class="text-xl font-bold mb-2">Múltiplos Níveis</h3>
          <p class="text-gray-600">Exames de A1 a C2, seguindo o padrão CILS</p>
        </div>

        <div class="feature-card bg-white rounded-lg shadow-lg p-6 text-center">
          <div class="text-5xl mb-4">📊</div>
          <h3 class="text-xl font-bold mb-2">Acompanhe seu Progresso</h3>
          <p class="text-gray-600">Estatísticas detalhadas por categoria e nível</p>
        </div>

        <div class="feature-card bg-white rounded-lg shadow-lg p-6 text-center">
          <div class="text-5xl mb-4">🎯</div>
          <h3 class="text-xl font-bold mb-2">Feedback Imediato</h3>
          <p class="text-gray-600">Saiba na hora se acertou ou errou</p>
        </div>
      </div>
    </div>

    <!-- Quick Stats -->
    <div class="container mx-auto px-4">
      <h2 class="text-3xl font-bold text-center mb-8">Seu Progresso</h2>
      <ProgressStats />
    </div>
  </div>
</template>

<script>
import ProgressStats from '@/components/ProgressStats.vue'

export default {
  name: 'HomeView',
  components: {
    ProgressStats
  }
}
</script>

<style scoped>
.home-view {
  min-height: 100vh;
  background-color: #f7fafc;
}

.feature-card {
  transition: transform 0.2s;
}

.feature-card:hover {
  transform: translateY(-8px);
}
</style>
