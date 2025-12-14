import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { 
      path: '/login', 
      name: 'login', 
      component: () => import('@/pages/LoginPage.vue'), 
      meta: { guest: true } 
    },
    { 
      path: '/register', 
      name: 'register', 
      component: () => import('@/pages/RegisterPage.vue'), 
      meta: { guest: true } 
    },
    {
      path: '/',
      component: () => import('@/layouts/AppLayout.vue'),
      meta: { auth: true },
      children: [
        { 
          path: '', 
          redirect: '/projects' 
        },
        { 
          path: 'projects', 
          name: 'projects', 
          component: () => import('@/pages/ProjectsPage.vue')
        },
        { 
          path: 'projects/new', 
          name: 'project-new', 
          component: () => import('@/pages/ProjectFormPage.vue')
        },
        { 
          path: 'projects/:id', 
          name: 'project-detail', 
          component: () => import('@/pages/ProjectDetailPage.vue')
        },
        { 
          path: 'projects/:id/edit', 
          name: 'project-edit', 
          component: () => import('@/pages/ProjectFormPage.vue')
        },
        { 
          path: 'projects/:id/timeline', 
          name: 'project-timeline', 
          component: () => import('@/pages/ProjectTimelinePage.vue')
        },
        { 
          path: 'projects/:id/board', 
          name: 'project-board', 
          component: () => import('@/pages/ProjectBoardPage.vue')
        },
      ]
    },
  ]
})

// Auth guard
router.beforeEach(async (to, _from, next) => {
  const authStore = useAuthStore()
  if (authStore.token && !authStore.user) {
    await authStore.fetchUser()
  }
  if (to.meta.auth && !authStore.isAuthenticated) {
    next('/login')
  } else if (to.meta.guest && authStore.isAuthenticated) {
    next('/projects')
  } else {
    next()
  }
})

export default router

