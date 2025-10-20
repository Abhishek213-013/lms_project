<template>
  <div class="main-area fix">
    <!-- Page Banner -->
    <section class="banner-area-two banner-bg-two" style="background-image: url('/assets/img/banner/banner_bg02.png')">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-xl-8 col-lg-10">
            <div class="banner__content-two text-center">
              <h3 class="title">Explore All Courses</h3>
              <p>Discover our comprehensive collection of courses designed to help you achieve your learning goals</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Courses Section -->
    <section class="courses-area section-py-120">
      <div class="container">
        <div class="row">
          <div class="col-lg-8">
            <!-- Courses Header -->
            <div class="courses__header mb-40">
              <div class="row align-items-center">
                <div class="col-md-6">
                  <h4 class="title">All Courses <span>({{ filteredCourses.length }} courses)</span></h4>
                </div>
                <div class="col-md-6">
                  <div class="courses__filter">
                    <select v-model="sortBy" class="form-select">
                      <option value="name">Sort by Name</option>
                      <option value="popular">Sort by Popularity</option>
                      <option value="newest">Sort by Newest</option>
                      <option value="rating">Sort by Rating</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <!-- Courses Grid -->
            <div class="row">
              <div class="col-lg-6 col-md-6" v-for="course in filteredCourses" :key="course.id">
                <div class="courses__item courses__item-two shine__animate-item">
                  <div class="courses__item-thumb courses__item-thumb-two">
                    <router-link :to="`/course/${course.slug}`" class="shine__animate-link">
                      <img :src="course.thumbnail || '/assets/img/courses/course_thumb01.png'" :alt="course.name">
                    </router-link>
                  </div>
                  <div class="courses__item-content courses__item-content-two">
                    <ul class="courses__item-meta list-wrap">
                      <li class="courses__item-tag">
                        <a href="#">{{ course.category || 'General' }}</a>
                      </li>
                      <li class="price" v-if="course.type === 'other' && course.fee">
                        ${{ course.fee }}
                      </li>
                      <li class="price free" v-else>
                        Free
                      </li>
                    </ul>
                    <h5 class="title">
                      <router-link :to="`/course/${course.slug}`">{{ course.name }}</router-link>
                    </h5>
                    <p class="course-description">{{ course.description || 'Comprehensive course covering all essential topics.' }}</p>
                    <div class="courses__item-content-bottom">
                      <div class="author-two">
                        <span v-if="course.teacher">
                          <img :src="course.teacher.avatar || '/assets/img/courses/course_author001.png'" :alt="course.teacher.name">
                          {{ course.teacher.name }}
                        </span>
                        <span v-else>
                          <img src="/assets/img/courses/course_author001.png" alt="Instructor">
                          Multiple Instructors
                        </span>
                      </div>
                      <div class="avg-rating">
                        <i class="fas fa-star"></i> ({{ course.rating || '4.8' }})
                      </div>
                    </div>
                  </div>
                  <div class="courses__item-bottom-two">
                    <ul class="list-wrap">
                      <li><i class="flaticon-book"></i>{{ course.subjectCount || 6 }} Lessons</li>
                      <li><i class="flaticon-mortarboard"></i>{{ course.studentCount || 25 }} Students</li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>

            <!-- Pagination -->
            <div class="pagination-wrap mt-50" v-if="totalPages > 1">
              <nav>
                <ul class="pagination list-wrap">
                  <li class="page-item" :class="{ disabled: currentPage === 1 }">
                    <a class="page-link" href="#" @click.prevent="changePage(currentPage - 1)">
                      <i class="fas fa-chevron-left"></i>
                    </a>
                  </li>
                  <li class="page-item" v-for="page in visiblePages" :key="page" :class="{ active: page === currentPage }">
                    <a class="page-link" href="#" @click.prevent="changePage(page)">{{ page }}</a>
                  </li>
                  <li class="page-item" :class="{ disabled: currentPage === totalPages }">
                    <a class="page-link" href="#" @click.prevent="changePage(currentPage + 1)">
                      <i class="fas fa-chevron-right"></i>
                    </a>
                  </li>
                </ul>
              </nav>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="col-lg-4">
            <div class="courses__sidebar">
              <!-- Search Widget -->
              <div class="courses__widget">
                <h4 class="courses__widget-title">Search Course</h4>
                <div class="courses__search">
                  <form @submit.prevent="searchCourses">
                    <input type="text" v-model="searchQuery" placeholder="Search courses...">
                    <button type="submit"><i class="flaticon-search"></i></button>
                  </form>
                </div>
              </div>

              <!-- Category Filter -->
              <div class="courses__widget">
                <h4 class="courses__widget-title">Categories</h4>
                <div class="courses__category">
                  <ul class="list-wrap">
                    <li v-for="category in categories" :key="category.id">
                      <label class="category-checkbox">
                        <input type="checkbox" :value="category.id" v-model="selectedCategories">
                        <span class="checkmark"></span>
                        {{ category.name }}
                        <span class="count">({{ category.count }})</span>
                      </label>
                    </li>
                  </ul>
                </div>
              </div>

              <!-- Course Type Filter -->
              <div class="courses__widget">
                <h4 class="courses__widget-title">Course Type</h4>
                <div class="courses__filter-type">
                  <ul class="list-wrap">
                    <li>
                      <label class="type-radio">
                        <input type="radio" name="courseType" value="" v-model="courseType">
                        <span class="radiomark"></span>
                        All Courses
                      </label>
                    </li>
                    <li>
                      <label class="type-radio">
                        <input type="radio" name="courseType" value="regular" v-model="courseType">
                        <span class="radiomark"></span>
                        Regular Classes
                      </label>
                    </li>
                    <li>
                      <label class="type-radio">
                        <input type="radio" name="courseType" value="other" v-model="courseType">
                        <span class="radiomark"></span>
                        Skill Courses
                      </label>
                    </li>
                  </ul>
                </div>
              </div>

              <!-- Featured Courses -->
              <div class="courses__widget">
                <h4 class="courses__widget-title">Featured Courses</h4>
                <div class="courses__featured">
                  <div class="featured-course" v-for="course in featuredCourses" :key="course.id">
                    <div class="featured-thumb">
                      <img :src="course.thumbnail || '/assets/img/courses/course_thumb01.png'" :alt="course.name">
                    </div>
                    <div class="featured-content">
                      <h6>
                        <router-link :to="`/course/${course.slug}`">{{ course.name }}</router-link>
                      </h6>
                      <div class="rating">
                        <i class="fas fa-star"></i> {{ course.rating }}
                      </div>
                      <div class="price" v-if="course.fee">${{ course.fee }}</div>
                      <div class="price free" v-else>Free</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  name: 'CoursesPage',
  data() {
    return {
      courses: [],
      featuredCourses: [],
      searchQuery: '',
      selectedCategories: [],
      courseType: '',
      sortBy: 'name',
      currentPage: 1,
      itemsPerPage: 6,
      categories: [
        { id: 'primary', name: 'Primary (1-5)', count: 25 },
        { id: 'junior', name: 'Junior (6-8)', count: 18 },
        { id: 'secondary', name: 'Secondary (9-10)', count: 15 },
        { id: 'higher-secondary', name: 'Higher Secondary (11-12)', count: 12 },
        { id: 'life-skills', name: 'Life Skills', count: 8 },
        { id: 'spoken-english', name: 'Spoken English', count: 6 }
      ]
    }
  },
  computed: {
    filteredCourses() {
      let filtered = this.courses;

      // Filter by search query
      if (this.searchQuery) {
        filtered = filtered.filter(course => 
          course.name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          (course.description && course.description.toLowerCase().includes(this.searchQuery.toLowerCase()))
        );
      }

      // Filter by categories
      if (this.selectedCategories.length > 0) {
        filtered = filtered.filter(course => 
          this.selectedCategories.includes(course.category)
        );
      }

      // Filter by course type
      if (this.courseType) {
        filtered = filtered.filter(course => course.type === this.courseType);
      }

      // Sort courses
      switch (this.sortBy) {
        case 'popular':
          filtered.sort((a, b) => (b.studentCount || 0) - (a.studentCount || 0));
          break;
        case 'newest':
          filtered.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
          break;
        case 'rating':
          filtered.sort((a, b) => (b.rating || 0) - (a.rating || 0));
          break;
        default:
          filtered.sort((a, b) => a.name.localeCompare(b.name));
      }

      // Pagination
      const start = (this.currentPage - 1) * this.itemsPerPage;
      const end = start + this.itemsPerPage;
      return filtered.slice(start, end);
    },
    totalPages() {
      return Math.ceil(this.courses.length / this.itemsPerPage);
    },
    visiblePages() {
      const pages = [];
      const total = this.totalPages;
      const current = this.currentPage;
      
      if (total <= 7) {
        for (let i = 1; i <= total; i++) pages.push(i);
      } else {
        if (current <= 4) {
          for (let i = 1; i <= 5; i++) pages.push(i);
          pages.push('...');
          pages.push(total);
        } else if (current >= total - 3) {
          pages.push(1);
          pages.push('...');
          for (let i = total - 4; i <= total; i++) pages.push(i);
        } else {
          pages.push(1);
          pages.push('...');
          for (let i = current - 1; i <= current + 1; i++) pages.push(i);
          pages.push('...');
          pages.push(total);
        }
      }
      return pages;
    }
  },
  async mounted() {
    await this.fetchCourses();
    await this.fetchFeaturedCourses();
  },
  watch: {
    searchQuery() {
      this.currentPage = 1;
    },
    selectedCategories() {
      this.currentPage = 1;
    },
    courseType() {
      this.currentPage = 1;
    },
    sortBy() {
      this.currentPage = 1;
    }
  },
  methods: {
    async fetchCourses() {
      try {
        const response = await this.$http.get('/api/courses');
        this.courses = response.data.data || this.getMockCourses();
      } catch (error) {
        console.error('Error fetching courses:', error);
        this.courses = this.getMockCourses();
      }
    },
    async fetchFeaturedCourses() {
      try {
        const response = await this.$http.get('/api/courses/featured');
        this.featuredCourses = response.data.data || this.getMockFeaturedCourses();
      } catch (error) {
        console.error('Error fetching featured courses:', error);
        this.featuredCourses = this.getMockFeaturedCourses();
      }
    },
    getMockCourses() {
      // Return mock data for development
      return [
        {
          id: 1,
          name: 'Mathematics - Class 10',
          slug: 'mathematics-class-10',
          category: 'secondary',
          type: 'regular',
          description: 'Comprehensive mathematics course for class 10 students covering all major topics.',
          studentCount: 45,
          subjectCount: 1,
          rating: '4.8',
          teacher: { name: 'John Smith', avatar: '/assets/img/courses/course_author001.png' },
          created_at: '2024-01-15'
        },
        {
          id: 2,
          name: 'Science - Class 8',
          slug: 'science-class-8',
          category: 'junior',
          type: 'regular',
          description: 'Interactive science course with practical experiments and demonstrations.',
          studentCount: 38,
          subjectCount: 1,
          rating: '4.7',
          teacher: { name: 'Sarah Johnson', avatar: '/assets/img/courses/course_author002.png' },
          created_at: '2024-02-10'
        },
        {
          id: 3,
          name: 'Life Skills Program',
          slug: 'life-skills-program',
          category: 'life-skills',
          type: 'other',
          description: 'Essential life skills training for personal and professional development.',
          studentCount: 25,
          subjectCount: 8,
          rating: '4.9',
          fee: 99,
          teacher: { name: 'Mike Wilson', avatar: '/assets/img/courses/course_author003.png' },
          created_at: '2024-01-20'
        },
        {
          id: 4,
          name: 'Spoken English Mastery',
          slug: 'spoken-english-mastery',
          category: 'spoken-english',
          type: 'other',
          description: 'Improve your English speaking skills with expert guidance and practice sessions.',
          studentCount: 32,
          subjectCount: 12,
          rating: '4.6',
          fee: 149,
          teacher: { name: 'Emma Davis', avatar: '/assets/img/courses/course_author004.png' },
          created_at: '2024-03-01'
        },
        {
          id: 5,
          name: 'Physics - Class 11',
          slug: 'physics-class-11',
          category: 'higher-secondary',
          type: 'regular',
          description: 'Advanced physics concepts and problem-solving techniques for class 11.',
          studentCount: 28,
          subjectCount: 1,
          rating: '4.5',
          teacher: { name: 'Dr. Robert Brown', avatar: '/assets/img/courses/course_author001.png' },
          created_at: '2024-02-15'
        },
        {
          id: 6,
          name: 'English Literature - Class 9',
          slug: 'english-literature-class-9',
          category: 'secondary',
          type: 'regular',
          description: 'Explore classic and contemporary English literature with critical analysis.',
          studentCount: 35,
          subjectCount: 1,
          rating: '4.7',
          teacher: { name: 'Lisa Anderson', avatar: '/assets/img/courses/course_author002.png' },
          created_at: '2024-01-25'
        }
      ];
    },
    getMockFeaturedCourses() {
      return this.getMockCourses().slice(0, 3);
    },
    searchCourses() {
      // Search is handled by the computed property
    },
    changePage(page) {
      if (page >= 1 && page <= this.totalPages && page !== this.currentPage) {
        this.currentPage = page;
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }
    }
  }
}
</script>

<<style scoped>
.courses__header {
  background: white;
  padding: 25px;
  border-radius: 10px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
  margin-bottom: 30px;
}

.courses__header .title {
  margin: 0;
  color: #2c3e50;
  font-size: 1.5rem;
  font-weight: 700;
}

.courses__header .title span {
  color: #7f8c8d;
  font-size: 1rem;
  font-weight: 400;
}

/* Course Items */
.courses__item {
  background: white;
  border-radius: 15px;
  overflow: hidden;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  margin-bottom: 30px;
  height: 100%;
}

.courses__item:hover {
  transform: translateY(-10px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.courses__item-thumb {
  position: relative;
  height: 200px;
  overflow: hidden;
}

.courses__item-thumb img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.courses__item:hover .courses__item-thumb img {
  transform: scale(1.1);
}

.courses__item-content {
  padding: 25px;
}

.courses__item-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
  padding: 0;
  list-style: none;
}

.courses__item-tag a {
  background: #e74c3c;
  color: white;
  padding: 4px 12px;
  border-radius: 15px;
  font-size: 0.8rem;
  font-weight: 600;
  text-decoration: none;
  text-transform: uppercase;
}

.courses__item-tag a:hover {
  background: #c0392b;
}

.price {
  color: #2c3e50;
  font-weight: 700;
  font-size: 1.1rem;
}

.price.free {
  color: #27ae60;
}

.courses__item-content .title {
  margin-bottom: 15px;
}

.courses__item-content .title a {
  color: #2c3e50;
  text-decoration: none;
  font-size: 1.3rem;
  font-weight: 700;
  line-height: 1.4;
  display: block;
  transition: color 0.3s ease;
}

.courses__item-content .title a:hover {
  color: #e74c3c;
}

/* Fixed line-clamp with complete browser compatibility */
.course-description {
  color: #7f8c8d;
  line-height: 1.6;
  margin-bottom: 20px;
  overflow: hidden;
  
  /* WebKit browsers (Chrome, Safari, newer Edge) */
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  
  /* Firefox */
  display: -moz-box;
  -moz-line-clamp: 2;
  -moz-box-orient: vertical;
  
  /* Standard property */
  display: box;
  line-clamp: 2;
  box-orient: vertical;
  
  /* Fallback for very old browsers */
  max-height: 3.2em;
  position: relative;
}

/* Gradient fade fallback */
.course-description::after {
  content: '';
  position: absolute;
  bottom: 0;
  right: 0;
  width: 70%;
  height: 1.2em;
  background: linear-gradient(to right, rgba(255,255,255,0) 0%, rgba(255,255,255,1) 50%);
  pointer-events: none;
}

/* Hide gradient when line-clamp is properly supported */
@supports (-webkit-line-clamp: 2) or (line-clamp: 2) or (-moz-line-clamp: 2) {
  .course-description::after {
    display: none;
  }
  
  .course-description {
    max-height: none;
  }
}

.courses__item-content-bottom {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 20px;
  padding-top: 20px;
  border-top: 1px solid #f8f9fa;
}

.author-two {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.9rem;
  color: #7f8c8d;
}

.author-two img {
  width: 30px;
  height: 30px;
  border-radius: 50%;
  object-fit: cover;
}

.avg-rating {
  color: #f39c12;
  font-weight: 600;
  font-size: 0.9rem;
}

.courses__item-bottom-two {
  padding: 20px 25px;
  background: #f8f9fa;
  border-top: 1px solid #e9ecef;
}

.courses__item-bottom-two .list-wrap {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0;
  margin: 0;
  list-style: none;
  color: #7f8c8d;
  font-size: 0.9rem;
}

.courses__item-bottom-two .list-wrap li {
  display: flex;
  align-items: center;
  gap: 5px;
}

/* Sidebar */
.courses__sidebar {
  position: sticky;
  top: 100px;
}

.courses__widget {
  background: white;
  padding: 25px;
  border-radius: 10px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
  margin-bottom: 30px;
}

.courses__widget-title {
  font-size: 1.3rem;
  color: #2c3e50;
  margin-bottom: 20px;
  padding-bottom: 15px;
  border-bottom: 2px solid #f8f9fa;
  font-weight: 700;
}

/* Search Widget */
.courses__search form {
  position: relative;
  display: flex;
}

.courses__search input {
  width: 100%;
  padding: 12px 45px 12px 15px;
  border: 2px solid #e9ecef;
  border-radius: 8px;
  font-size: 1rem;
  background: #f8f9fa;
  transition: all 0.3s ease;
}

.courses__search input:focus {
  outline: none;
  border-color: #e74c3c;
  background: white;
  box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
}

.courses__search button {
  position: absolute;
  right: 5px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: #7f8c8d;
  cursor: pointer;
  padding: 8px;
  transition: color 0.3s ease;
}

.courses__search button:hover {
  color: #e74c3c;
}

/* Category Filter */
.courses__category .list-wrap {
  list-style: none;
  padding: 0;
  margin: 0;
}

.courses__category .list-wrap li {
  margin-bottom: 12px;
}

.courses__category .list-wrap li:last-child {
  margin-bottom: 0;
}

.category-checkbox {
  display: flex;
  align-items: center;
  gap: 10px;
  cursor: pointer;
  font-size: 0.95rem;
  color: #2c3e50;
  transition: color 0.3s ease;
  padding: 5px 0;
}

.category-checkbox:hover {
  color: #e74c3c;
}

.category-checkbox input[type="checkbox"] {
  display: none;
}

.checkmark {
  width: 18px;
  height: 18px;
  border: 2px solid #e9ecef;
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
  flex-shrink: 0;
}

.category-checkbox input[type="checkbox"]:checked + .checkmark {
  background: #e74c3c;
  border-color: #e74c3c;
}

.category-checkbox input[type="checkbox"]:checked + .checkmark::after {
  content: '✓';
  color: white;
  font-size: 12px;
  font-weight: bold;
}

.count {
  color: #7f8c8d;
  font-size: 0.8rem;
  margin-left: auto;
}

/* Course Type Filter */
.courses__filter-type .list-wrap {
  list-style: none;
  padding: 0;
  margin: 0;
}

.courses__filter-type .list-wrap li {
  margin-bottom: 12px;
}

.courses__filter-type .list-wrap li:last-child {
  margin-bottom: 0;
}

.type-radio {
  display: flex;
  align-items: center;
  gap: 10px;
  cursor: pointer;
  font-size: 0.95rem;
  color: #2c3e50;
  transition: color 0.3s ease;
  padding: 5px 0;
}

.type-radio:hover {
  color: #e74c3c;
}

.type-radio input[type="radio"] {
  display: none;
}

.radiomark {
  width: 18px;
  height: 18px;
  border: 2px solid #e9ecef;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s ease;
  flex-shrink: 0;
}

.type-radio input[type="radio"]:checked + .radiomark {
  border-color: #e74c3c;
}

.type-radio input[type="radio"]:checked + .radiomark::after {
  content: '';
  width: 8px;
  height: 8px;
  background: #e74c3c;
  border-radius: 50%;
}

/* Featured Courses */
.courses__featured {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.featured-course {
  display: flex;
  gap: 15px;
  align-items: flex-start;
  padding-bottom: 20px;
  border-bottom: 1px solid #f8f9fa;
}

.featured-course:last-child {
  padding-bottom: 0;
  border-bottom: none;
}

.featured-thumb {
  width: 70px;
  height: 70px;
  border-radius: 8px;
  overflow: hidden;
  flex-shrink: 0;
}

.featured-thumb img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.featured-course:hover .featured-thumb img {
  transform: scale(1.1);
}

.featured-content {
  flex: 1;
}

.featured-content h6 {
  margin-bottom: 8px;
  line-height: 1.4;
}

.featured-content h6 a {
  color: #2c3e50;
  text-decoration: none;
  font-size: 0.95rem;
  font-weight: 600;
  transition: color 0.3s ease;
}

.featured-content h6 a:hover {
  color: #e74c3c;
}

.featured-content .rating {
  color: #f39c12;
  font-size: 0.8rem;
  margin-bottom: 5px;
  font-weight: 600;
}

.featured-content .price {
  color: #2c3e50;
  font-weight: 700;
  font-size: 0.9rem;
}

.featured-content .price.free {
  color: #27ae60;
}

/* Form Select */
.form-select {
  width: 100%;
  padding: 10px 15px;
  border: 2px solid #e9ecef;
  border-radius: 8px;
  font-size: 1rem;
  background: #f8f9fa;
  transition: all 0.3s ease;
  cursor: pointer;
  appearance: none;
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
  background-position: right 12px center;
  background-repeat: no-repeat;
  background-size: 16px;
  padding-right: 40px;
}

.form-select:focus {
  outline: none;
  border-color: #e74c3c;
  background: white;
  box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
}

/* Pagination */
.pagination-wrap {
  display: flex;
  justify-content: center;
  margin-top: 50px;
}

.pagination {
  display: flex;
  gap: 5px;
  list-style: none;
  padding: 0;
  margin: 0;
  flex-wrap: wrap;
}

.page-item {
  margin: 2px;
}

.page-link {
  color: #2c3e50;
  border: 1px solid #e9ecef;
  border-radius: 5px;
  padding: 10px 15px;
  text-decoration: none;
  display: block;
  transition: all 0.3s ease;
  background: white;
  font-weight: 500;
  min-width: 45px;
  text-align: center;
}

.page-link:hover {
  background: #f8f9fa;
  border-color: #e74c3c;
  color: #e74c3c;
}

.page-item.active .page-link {
  background-color: #e74c3c;
  border-color: #e74c3c;
  color: white;
}

.page-item.disabled .page-link {
  color: #6c757d;
  pointer-events: none;
  opacity: 0.6;
}

.page-item:not(.disabled) .page-link {
  cursor: pointer;
}

/* Responsive Design */
@media (max-width: 991px) {
  .courses__sidebar {
    position: static;
    margin-top: 50px;
  }
}

@media (max-width: 768px) {
  .courses__header {
    padding: 20px;
    text-align: center;
  }
  
  .courses__header .row {
    gap: 15px;
  }
  
  .courses__widget {
    padding: 20px;
  }
  
  .courses__item-content {
    padding: 20px;
  }
  
  .courses__item-content-bottom {
    flex-direction: column;
    gap: 15px;
    align-items: flex-start;
  }
  
  .courses__item-bottom-two .list-wrap {
    flex-direction: column;
    gap: 10px;
    align-items: flex-start;
  }
  
  .featured-course {
    flex-direction: column;
    text-align: center;
    gap: 10px;
  }
  
  .featured-thumb {
    width: 100%;
    height: 120px;
  }
  
  .pagination {
    justify-content: center;
  }
}

@media (max-width: 480px) {
  .courses__item {
    margin-bottom: 20px;
  }
  
  .courses__item-content {
    padding: 15px;
  }
  
  .courses__item-content .title a {
    font-size: 1.1rem;
  }
  
  .course-description {
    font-size: 0.9rem;
  }
  
  .courses__widget {
    padding: 15px;
  }
  
  .page-link {
    padding: 8px 12px;
    font-size: 0.9rem;
    min-width: 40px;
  }
}

/* Focus styles for accessibility */
.form-select:focus,
.courses__search input:focus,
.category-checkbox input:focus + .checkmark,
.type-radio input:focus + .radiomark,
.page-link:focus,
.featured-content h6 a:focus,
.courses__item-content .title a:focus {
  outline: 2px solid #e74c3c;
  outline-offset: 2px;
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
  .courses__item,
  .courses__item-thumb img,
  .featured-thumb img,
  .page-link,
  .form-select,
  .courses__search input,
  .category-checkbox,
  .type-radio {
    transition: none;
  }
  
  .courses__item:hover {
    transform: none;
  }
  
  .courses__item:hover .courses__item-thumb img,
  .featured-course:hover .featured-thumb img {
    transform: none;
  }
}

/* Shine animation for course items */
.shine__animate-item {
  position: relative;
  overflow: hidden;
}

.shine__animate-link::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(
    90deg,
    transparent,
    rgba(255, 255, 255, 0.4),
    transparent
  );
  transition: left 0.5s ease;
}

.shine__animate-item:hover .shine__animate-link::before {
  left: 100%;
}
</style>