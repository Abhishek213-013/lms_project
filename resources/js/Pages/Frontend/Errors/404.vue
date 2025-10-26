<template>
  <main class="main-area fix">
    <!-- breadcrumb-area -->
    <section class="breadcrumb__area breadcrumb__bg" :style="breadcrumbStyle">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <div class="breadcrumb__content">
              <h3 class="title">Page Not Found</h3>
              <nav class="breadcrumb">
                <span property="itemListElement" typeof="ListItem">
                  <Link href="/">Home</Link>
                </span>
                <span class="breadcrumb-separator"><i class="fas fa-angle-right"></i></span>
                <span property="itemListElement" typeof="ListItem">404 Error</span>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- breadcrumb-area-end -->

    <!-- error-area -->
    <section class="error__area section-py-120">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-xl-8">
            <div class="error__content text-center">
              <div class="error__thumb mb-45">
                <img src="/assets/img/others/error.png" alt="404 Error">
              </div>
              <div class="error__content-box">
                <h2 class="title">Page Not Found!</h2>
                <p>{{ message || 'The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.' }}</p>
                <div class="error__btn-wrap">
                  <Link href="/" class="btn btn-primary">
                    <span class="text">Back to Homepage</span>
                  </Link>
                  <Link href="/courses" class="btn btn-secondary">
                    <span class="text">Browse Courses</span>
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- error-area-end -->
  </main>
</template>

<script>
import { Link } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

export default {
  name: 'NotFoundPage',
  components: {
    Link
  },
  props: {
    message: {
      type: String,
      default: ''
    }
  },
  setup() {
    const breadcrumbStyle = computed(() => {
      return {
        background: 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)'
      };
    });

    return {
      breadcrumbStyle
    };
  }
}
</script>

<style scoped>
.error__area {
  text-align: center;
}

.error__thumb img {
  max-width: 400px;
  width: 100%;
}

.error__content-box .title {
  font-size: 2.5rem;
  color: #2d3748;
  margin-bottom: 1rem;
}

.error__content-box p {
  font-size: 1.1rem;
  color: #718096;
  margin-bottom: 2rem;
  max-width: 500px;
  margin-left: auto;
  margin-right: auto;
}

.error__btn-wrap {
  display: flex;
  gap: 1rem;
  justify-content: center;
  flex-wrap: wrap;
}

@media (max-width: 768px) {
  .error__content-box .title {
    font-size: 2rem;
  }
  
  .error__btn-wrap {
    flex-direction: column;
    align-items: center;
  }
  
  .error__btn-wrap .btn {
    width: 100%;
    max-width: 250px;
  }
}
</style>