<template>
  <div class="home-page">
    <!-- Hero Section -->
    <section 
      class="hero-section"
      :style="{
        background: `linear-gradient(135deg, rgba(245, 247, 250, 0.9) 0%, rgba(228, 232, 240, 0.9) 100%), url('/assets/img/banner/banner_bg02.png') center/cover no-repeat`
      }"
    >
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-6">
            <div class="hero-content">
              <h1 class="hero-title">Learning is What You Make of it. Make it Yours at SkillGro.</h1>
              <p class="hero-subtitle">Unlock your potential with our expert-led courses and transform your career.</p>
              
              <div class="hero-actions">
                <router-link to="/registration" class="btn btn-hero-primary">Start Free Trial</router-link>
                <a href="#" class="btn btn-hero-secondary">
                  <i class="fas fa-play-circle"></i>
                  Watch Our Class Drone
                </a>
              </div>
              
              <div class="hero-stats">
                <div class="stat-item">
                  <span class="stat-number">15K</span>
                  <span class="stat-label">Text Students</span>
                </div>
                <div class="stat-item">
                  <span class="stat-number">38K+</span>
                  <span class="stat-label">Enrolled Students</span>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="hero-image">
              <img src="/assets/img/h2_banner_img.png" alt="Online Learning" class="img-fluid">
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Features Section -->
    <section class="features-section py-5">
      <div class="container">
        <div class="row text-center mb-5">
          <div class="col-12">
            <h2 class="fw-bold mb-3">Why Choose SkillGro?</h2>
            <p class="lead">Learn from industry experts and advance your career</p>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4 mb-4" v-for="feature in features" :key="feature.id">
            <div class="feature-card text-center p-4">
              <div class="feature-icon mb-3">
                <i :class="feature.icon" class="text-primary"></i>
              </div>
              <h5 class="fw-bold">{{ feature.title }}</h5>
              <p class="text-muted">{{ feature.description }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Popular Courses -->
    <section class="courses-section py-5 bg-light">
      <div class="container">
        <div class="row mb-5">
          <div class="col-12 text-center">
            <h2 class="fw-bold mb-3">Popular Courses</h2>
            <p class="lead">Discover our most enrolled courses</p>
          </div>
        </div>
        <div class="row">
          <div class="col-md-4 mb-4" v-for="course in popularCourses" :key="course.id">
            <div class="card course-card h-100">
              <img :src="course.image" class="card-img-top" :alt="course.title">
              <div class="card-body">
                <h5 class="card-title">{{ course.title }}</h5>
                <p class="card-text text-muted">{{ course.description }}</p>
                <div class="course-meta d-flex justify-content-between align-items-center">
                  <span class="badge bg-primary">{{ course.category }}</span>
                  <span class="text-muted">{{ course.duration }}</span>
                </div>
              </div>
              <div class="card-footer bg-transparent">
                <router-link :to="`/course/${course.slug}`" class="btn btn-primary w-100">
                  View Course
                </router-link>
              </div>
            </div>
          </div>
        </div>
        <div class="row mt-4">
          <div class="col-12 text-center">
            <router-link to="/courses" class="btn btn-outline-primary btn-lg">
              View All Courses
            </router-link>
          </div>
        </div>
      </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section py-5 bg-primary text-white">
      <div class="container">
        <div class="row text-center">
          <div class="col-md-3 mb-4" v-for="stat in stats" :key="stat.id">
            <h3 class="fw-bold display-4">{{ stat.value }}</h3>
            <p class="mb-0">{{ stat.label }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section py-5">
      <div class="container">
        <div class="row text-center">
          <div class="col-12">
            <h2 class="fw-bold mb-3">Ready to Start Learning?</h2>
            <p class="lead mb-4">Join thousands of students already learning with SkillGro</p>
            <router-link to="/registration" class="btn btn-primary btn-lg">
              Get Started Today
            </router-link>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  name: 'HomePage',
  data() {
    return {
      features: [
        {
          id: 1,
          title: 'Expert Instructors',
          description: 'Learn from industry professionals with real-world experience',
          icon: 'fas fa-chalkboard-teacher fa-3x'
        },
        {
          id: 2,
          title: 'Flexible Learning',
          description: 'Study at your own pace with 24/7 access to course materials',
          icon: 'fas fa-laptop-house fa-3x'
        },
        {
          id: 3,
          title: 'Career Support',
          description: 'Get career guidance and certification for your achievements',
          icon: 'fas fa-graduation-cap fa-3x'
        }
      ],
      popularCourses: [
        {
          id: 1,
          title: 'Mathematics - Class 10',
          description: 'Comprehensive mathematics course covering all major topics',
          category: 'Secondary',
          duration: '30 hours',
          image: '/assets/img/courses/math.jpg',
          slug: 'mathematics-class-10'
        },
        {
          id: 2,
          title: 'Science - Class 8',
          description: 'Interactive science course with practical experiments',
          category: 'Junior',
          duration: '25 hours',
          image: '/assets/img/courses/science.jpg',
          slug: 'science-class-8'
        },
        {
          id: 3,
          title: 'Life Skills Program',
          description: 'Essential life skills for personal and professional growth',
          category: 'Life Skills',
          duration: '20 hours',
          image: '/assets/img/courses/life-skills.jpg',
          slug: 'life-skills-program'
        }
      ],
      stats: [
        { id: 1, value: '50K+', label: 'Students Enrolled' },
        { id: 2, value: '200+', label: 'Expert Instructors' },
        { id: 3, value: '150+', label: 'Courses Available' },
        { id: 4, value: '95%', label: 'Success Rate' }
      ]
    }
  }
}
</script>

<style scoped>
/* Hero Section Styles */
.hero-section {
  padding: 120px 0 80px;
  position: relative;
  overflow: hidden;
  min-height: 80vh;
  display: flex;
  align-items: center;
}

.hero-content {
  max-width: 600px;
}

.hero-title {
  font-size: 3.5rem;
  font-weight: 700;
  line-height: 1.2;
  margin-bottom: 1.5rem;
  color: var(--secondary-color, #2c3e50);
}

.hero-subtitle {
  font-size: 1.25rem;
  color: #6c757d;
  margin-bottom: 2.5rem;
}

.hero-actions {
  display: flex;
  gap: 1rem;
  margin-bottom: 3rem;
}

.btn-hero-primary {
  background-color: var(--primary-color, #e74c3c);
  border-color: var(--primary-color, #e74c3c);
  color: white;
  padding: 0.75rem 2rem;
  font-weight: 600;
  border-radius: 50px;
  text-decoration: none;
  transition: all 0.3s ease;
}

.btn-hero-primary:hover {
  background-color: #c0392b;
  border-color: #c0392b;
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 10px 20px rgba(231, 76, 60, 0.3);
}

.btn-hero-secondary {
  background-color: transparent;
  color: var(--primary-color, #e74c3c);
  border: 2px solid var(--primary-color, #e74c3c);
  padding: 0.75rem 2rem;
  font-weight: 600;
  border-radius: 50px;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  transition: all 0.3s ease;
  text-decoration: none;
}

.btn-hero-secondary:hover {
  background-color: var(--primary-color, #e74c3c);
  color: white;
  transform: translateY(-2px);
  box-shadow: 0 10px 20px rgba(231, 76, 60, 0.3);
}

.hero-stats {
  display: flex;
  gap: 2rem;
}

.stat-item {
  display: flex;
  flex-direction: column;
}

.stat-number {
  font-size: 2rem;
  font-weight: 700;
  color: var(--secondary-color, #2c3e50);
  line-height: 1;
}

.stat-label {
  font-size: 0.9rem;
  color: #6c757d;
}

.hero-image {
  position: relative;
  text-align: center;
  animation: float 3s ease-in-out infinite;
}

.hero-image img {
  max-width: 100%;
  border-radius: 10px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

@keyframes float {
  0%, 100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-20px);
  }
}

/* Existing Styles */
.feature-card {
  border-radius: 15px;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  border: 1px solid #e9ecef;
}

.feature-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
}

.course-card {
  transition: transform 0.3s ease;
  border: none;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
}

.course-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15);
}

.stats-section {
  background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
}

.cta-section {
  background: #f8f9fa;
}

/* Responsive Design */
@media (max-width: 768px) {
  .hero-section {
    padding: 80px 0 60px;
    min-height: auto;
    text-align: center;
  }
  
  .hero-title {
    font-size: 2.5rem;
  }
  
  .hero-actions {
    flex-direction: column;
    align-items: center;
  }
  
  .hero-stats {
    justify-content: center;
  }
  
  .stat-number {
    font-size: 1.75rem;
  }
  
  .hero-image {
    margin-top: 3rem;
  }
}

@media (max-width: 576px) {
  .hero-title {
    font-size: 2rem;
  }
  
  .hero-subtitle {
    font-size: 1.1rem;
  }
  
  .btn-hero-primary,
  .btn-hero-secondary {
    padding: 0.6rem 1.5rem;
    font-size: 0.9rem;
  }
  
  .hero-stats {
    flex-direction: column;
    gap: 1rem;
  }
}
</style>