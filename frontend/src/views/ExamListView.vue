<template>
  <div class="exam-list-view">
    <div class="container mx-auto px-4 py-8">
      <ExamList />
    </div>
  </div>
</template>

<script>
import ExamList from '@/components/ExamList.vue'

export default {
  name: 'ExamListView',
  components: {
    ExamList
  }
}
</script>

<style scoped>
.exam-list-view {
  min-height: 100vh;
  background-color: #f7fafc;
}
</style>
