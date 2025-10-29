<template>
  <div class="registration-page">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
          <div class="registration-card">
            <div class="text-center mb-4">
              <h2>Student Registration</h2>
              <p class="text-muted">Complete your profile to get started</p>
            </div>

            <!-- Success Message -->
            <div v-if="status" class="alert alert-success alert-dismissible fade show" role="alert">
              {{ status }}
              <button type="button" class="btn-close" @click="status = ''"></button>
            </div>

            <form @submit.prevent="submit">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="name" class="form-label">Full Name *</label>
                  <input
                    type="text"
                    class="form-control"
                    id="name"
                    v-model="form.name"
                    placeholder="Enter your full name"
                    required
                    :class="{ 'is-invalid': form.errors.name }"
                  >
                  <div v-if="form.errors.name" class="invalid-feedback">
                    {{ form.errors.name }}
                  </div>
                </div>

                <div class="col-md-6 mb-3">
                  <label for="username" class="form-label">Username *</label>
                  <input
                    type="text"
                    class="form-control"
                    id="username"
                    v-model="form.username"
                    placeholder="Choose a username"
                    required
                    :class="{ 'is-invalid': form.errors.username }"
                  >
                  <div v-if="form.errors.username" class="invalid-feedback">
                    {{ form.errors.username }}
                  </div>
                </div>
              </div>

              <div class="mb-3">
                <label for="email" class="form-label">Email Address *</label>
                <input
                  type="email"
                  class="form-control"
                  id="email"
                  v-model="form.email"
                  placeholder="Enter your email address"
                  required
                  :class="{ 'is-invalid': form.errors.email }"
                >
                <div v-if="form.errors.email" class="invalid-feedback">
                  {{ form.errors.email }}
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="father_name" class="form-label">Father's Name *</label>
                  <input
                    type="text"
                    class="form-control"
                    id="father_name"
                    v-model="form.father_name"
                    placeholder="Enter father's name"
                    required
                    :class="{ 'is-invalid': form.errors.father_name }"
                  >
                  <div v-if="form.errors.father_name" class="invalid-feedback">
                    {{ form.errors.father_name }}
                  </div>
                </div>

                <div class="col-md-6 mb-3">
                  <label for="mother_name" class="form-label">Mother's Name *</label>
                  <input
                    type="text"
                    class="form-control"
                    id="mother_name"
                    v-model="form.mother_name"
                    placeholder="Enter mother's name"
                    required
                    :class="{ 'is-invalid': form.errors.mother_name }"
                  >
                  <div v-if="form.errors.mother_name" class="invalid-feedback">
                    {{ form.errors.mother_name }}
                  </div>
                </div>
              </div>

              <div class="mb-3">
                <label for="class_id" class="form-label">Class *</label>
                <select class="form-select" id="class_id" v-model="form.class_id" required :class="{ 'is-invalid': form.errors.class_id }">
                  <option value="">Select Class</option>
                  <option value="1">Class 1</option>
                  <option value="2">Class 2</option>
                  <option value="3">Class 3</option>
                  <option value="4">Class 4</option>
                  <option value="5">Class 5</option>
                  <option value="6">Class 6</option>
                  <option value="7">Class 7</option>
                  <option value="8">Class 8</option>
                  <option value="9">Class 9</option>
                  <option value="10">Class 10</option>
                </select>
                <div v-if="form.errors.class_id" class="invalid-feedback">
                  {{ form.errors.class_id }}
                </div>
              </div>

              <div class="mb-3">
                <label for="parent_contact" class="form-label">Parent's Contact Number *</label>
                <div class="input-group">
                  <select class="form-select country-code" v-model="form.country_code" :class="{ 'is-invalid': form.errors.country_code }">
                    <option value="+880">Bangladesh (+880)</option>
                    <option value="+91">India (+91)</option>
                    <option value="+1">USA (+1)</option>
                    <option value="+44">UK (+44)</option>
                    <option value="+971">UAE (+971)</option>
                  </select>
                  <input
                    type="tel"
                    class="form-control"
                    id="parent_contact"
                    v-model="form.parent_contact"
                    placeholder="Enter parent's contact number"
                    required
                    :class="{ 'is-invalid': form.errors.parent_contact }"
                  >
                </div>
                <div v-if="form.errors.parent_contact" class="invalid-feedback">
                  {{ form.errors.parent_contact }}
                </div>
                <div v-if="form.errors.country_code" class="invalid-feedback">
                  {{ form.errors.country_code }}
                </div>
              </div>

              <div class="mb-3">
                <label for="password" class="form-label">Password *</label>
                <input
                  type="password"
                  class="form-control"
                  id="password"
                  v-model="form.password"
                  placeholder="Create a password"
                  required
                  :class="{ 'is-invalid': form.errors.password }"
                >
                <div v-if="form.errors.password" class="invalid-feedback">
                  {{ form.errors.password }}
                </div>
              </div>

              <div class="mb-3">
                <label for="password_confirmation" class="form-label">Confirm Password *</label>
                <input
                  type="password"
                  class="form-control"
                  id="password_confirmation"
                  v-model="form.password_confirmation"
                  placeholder="Confirm your password"
                  required
                  :class="{ 'is-invalid': form.errors.password_confirmation }"
                >
                <div v-if="form.errors.password_confirmation" class="invalid-feedback">
                  {{ form.errors.password_confirmation }}
                </div>
              </div>

              <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="terms" v-model="form.terms" required :class="{ 'is-invalid': form.errors.terms }">
                <label class="form-check-label" for="terms">
                  I agree to the <a href="/terms" class="text-decoration-none">Terms and Conditions</a>
                </label>
                <div v-if="form.errors.terms" class="invalid-feedback">
                  {{ form.errors.terms }}
                </div>
              </div>

              <button type="submit" class="btn btn-primary w-100" :disabled="form.processing">
                <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                {{ form.processing ? 'Creating Account...' : 'Create Account' }}
              </button>
            </form>

            <div class="text-center mt-3">
              <a href="/student-login" class="text-decoration-none">
                Already have an account? Login here
              </a>
            </div>

            <!-- Admin/Teacher Registration Link -->
            <div class="text-center mt-4 pt-3 border-top">
              <p class="text-muted small mb-2">Are you an admin or teacher?</p>
              <a href="/registration" class="btn btn-sm btn-outline-secondary">
                Admin/Teacher Registration
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
  errors: Object,
  status: String,
  phone: String,
});

const form = useForm({
  name: '',
  username: '',
  email: '',
  father_name: '',
  mother_name: '',
  class_id: '',
  country_code: '+880',
  parent_contact: props.phone || '',
  password: '',
  password_confirmation: '',
  terms: false,
});

const submit = () => {
  form.post('/student-registration', {
    preserveScroll: true,
    onSuccess: () => {
      console.log('✅ Student registration successful');
    },
    onError: (errors) => {
      console.log('❌ Student registration errors:', errors);
    },
  });
};
</script>

<style scoped>
.registration-page {
  min-height: 100vh;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  display: flex;
  align-items: center;
  padding: 20px 0;
}

.registration-card {
  background: white;
  padding: 2.5rem;
  border-radius: 15px;
  box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
}

.form-control, .form-select {
  padding: 0.75rem 1rem;
  border-radius: 10px;
  border: 2px solid #e9ecef;
  transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
  border-color: #e74c3c;
  box-shadow: 0 0 0 0.2rem rgba(231, 76, 60, 0.25);
}

.country-code {
  max-width: 180px;
}

.btn {
  padding: 0.75rem 1.5rem;
  border-radius: 10px;
  font-weight: 600;
}

@media (max-width: 576px) {
  .registration-card {
    padding: 2rem 1.5rem;
  }
  
  .country-code {
    max-width: 140px;
  }
}
</style>