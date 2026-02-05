<template>
    <div class="d-flex align-items-center justify-content-center" style="min-height:75vh;">
        <div class="card p-4" style="width:380px;">
            <h3 class="mb-3">HR Portal — Login</h3>
            <form @submit.prevent="onSubmit" novalidate>
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input v-model="username" type="text" class="form-control" required />
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input v-model="password" type="password" class="form-control" required />
                </div>
                <div v-if="error" class="alert alert-danger py-1">{{ error }}</div>
                    <button class="btn btn-primary w-100" :disabled="loading">{{ loading ? 'Signing in...' : 'Sign in' }}</button>
            </form>
            <p class="text-muted small mt-2">Demo: <strong>hradmin / Password123</strong></p>
        </div>
    </div>
</template>
<script setup>
import { ref } from 'vue'
import { useAuthStore } from '../store/hrStore'
import { useRouter } from 'vue-router'
const username = ref('')
const password = ref('')
const loading = ref(false)
const error = ref('')
const auth = useAuthStore()
const router = useRouter()
function onSubmit() {
    loading.value = true
    error.value = ''
    setTimeout(() => {
        const ok = auth.login(username.value, password.value)
        loading.value = false
        if (!ok) {
            error.value = 'Invalid credentials. Try hradmin / Password123'
            return
        }
        router.push('/dashboard')
    }, 400)
}
</script>





















