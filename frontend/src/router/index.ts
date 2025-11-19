import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/exams',
      name: 'exams',
      component: () => import('../views/ExamListView.vue'),
    },
    {
      path: '/exam/:id',
      name: 'exam',
      component: () => import('../views/QuizView.vue'),
    },
    {
      path: '/about',
      name: 'about',
      component: () => import('../views/AboutView.vue'),
    },
    {
      path: '/courses',
      name: 'courses',
      component: () => import('../views/CourseListView.vue'),
    },
    {
      path: '/courses/:id',
      name: 'course-detail',
      component: () => import('../views/CourseDetailView.vue'),
    },
    {
      path: '/lesson/:id',
      name: 'lesson',
      component: () => import('../views/LessonView.vue'),
    },
  ],
})

export default router
