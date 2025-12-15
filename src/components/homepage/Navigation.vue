<template>
  <nav
    :class="[
      'fixed top-0 left-0 right-0 z-50 transition-all duration-300',
      isScrolled
        ? 'bg-white/80 backdrop-blur-xl border-b border-slate-200/50'
        : 'bg-white/80 backdrop-blur-xl',
    ]"
  >
    <div class="max-w-7xl mx-auto px-6 lg:px-8">
      <div class="flex items-center justify-between h-16">
        <div class="flex items-center">
          <span class="text-xl font-semibold text-slate-900"> 4 Projects </span>
        </div>

        <div class="hidden md:flex items-center space-x-10">
          <a
            v-for="link in navLinks"
            :key="link.name"
            :href="link.href"
            class="text-sm text-slate-600 hover:text-slate-900 transition-colors"
          >
            {{ link.name }}
          </a>
        </div>

        <div class="hidden md:flex items-center space-x-6">
          <router-link
            to="/login"
            class="text-sm text-slate-600 hover:text-slate-900 transition-colors"
          >
            Sign In
          </router-link>
          <Button size="sm" @click="handleGetStarted">Start Free</Button>
        </div>
      </div>
    </div>
  </nav>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import Button from '@/components/ui/Button.vue'

const router = useRouter()
const authStore = useAuthStore()

const isScrolled = ref(false)

const navLinks = [
  { name: 'Product', href: '#product' },
  { name: 'Features', href: '#features' },
  { name: 'Pricing', href: '#pricing' },
  { name: 'Resources', href: '#resources' },
  { name: 'Enterprise', href: '#enterprise' },
]

const handleScroll = () => {
  isScrolled.value = window.scrollY > 20
}

const handleGetStarted = () => {
  if (authStore.isAuthenticated) {
    router.push('/projects/new')
  } else {
    router.push('/register')
  }
}

onMounted(() => {
  window.addEventListener('scroll', handleScroll)
})

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll)
})
</script>

