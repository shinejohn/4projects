<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <div>
        <div class="flex justify-center">
          <div class="w-16 h-16 bg-primary-600 rounded-full flex items-center justify-center text-white text-2xl font-bold">
            4P
          </div>
        </div>
        <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">
          Sign in to Projects.ai
        </h2>
        <p class="mt-2 text-center text-sm text-gray-600">
          Organize and manage your projects with ease
        </p>
      </div>
      <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
        <LoginForm
          app-name="Projects.ai"
          @success="handleSuccess"
          @sign-up="handleSignUp"
          @forgot-password="handleForgotPassword"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import LoginForm from '@/components/auth/LoginForm.vue'

const router = useRouter()
const authStore = useAuthStore()

function handleSuccess() {
  // Update auth store with user data
  authStore.fetchUser()
  router.push({ name: 'projects' })
}

function handleSignUp() {
  router.push({ name: 'register' })
}

function handleForgotPassword() {
  router.push({ name: 'forgot-password' })
}
</script>
