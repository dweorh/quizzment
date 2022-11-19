import { createRouter, createWebHistory } from 'vue-router'
import CategorySelectionView from '../views/CategorySelectionView.vue'
import LessonSelectionView from '../views/LessonSelectionView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'category',
      component: CategorySelectionView
    },
    {
      path: '/lesson',
      name: 'lesson',
      component: LessonSelectionView
    },
    {
      path: '/practice',
      name: 'practice',
      component: () => import('../views/PracticeView.vue')
    },
    {
      path: '/exam',
      name: 'exam',
      component: () => import('../views/ExamView.vue')
    },
    {
      path: '/result/:result_id',
      name: 'result',
      component: () => import('../views/ExamView.vue'),
      props: true
    }
  ]
})

export default router
