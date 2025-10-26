<template>
  <div class="main-area fix">
    <!-- Page Banner -->
    <section class="banner-area-two banner-bg-two" style="background-image: url('/assets/img/banner/banner_bg02.png')">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-xl-8 col-lg-10">
            <div class="banner__content-two text-center">
              <h3 class="title">SkillGro Blog</h3>
              <p>Insights, tips, and updates from the world of education and online learning</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Blog Section -->
    <section class="blog-area section-py-120">
      <div class="container">
        <div class="row">
          <!-- Main Content -->
          <div class="col-lg-8">
            <!-- Blog Header -->
            <div class="blog-header mb-40">
              <div class="row align-items-center">
                <div class="col-md-6">
                  <h4 class="blog-title">Latest Articles <span>({{ filteredPosts.length }} posts)</span></h4>
                </div>
                <div class="col-md-6">
                  <div class="blog-filter">
                    <select v-model="filterCategory" class="form-select">
                      <option value="">All Categories</option>
                      <option value="education">Education</option>
                      <option value="technology">Technology</option>
                      <option value="career">Career</option>
                      <option value="tips">Learning Tips</option>
                      <option value="news">News & Updates</option>
                    </select>
                  </div>
                </div>
              </div>
            </div>

            <!-- Blog Posts -->
            <div class="blog-posts">
              <div class="row">
                <div class="col-md-6" v-for="post in filteredPosts" :key="post.id">
                  <article class="blog-post">
                    <div class="post-thumbnail">
                      <router-link :to="`/blog/${post.slug}`">
                        <img :src="post.image" :alt="post.title">
                      </router-link>
                      <div class="post-category">
                        <span :class="`category-${post.category}`">{{ post.category }}</span>
                      </div>
                    </div>
                    <div class="post-content">
                      <div class="post-meta">
                        <span class="post-date">
                          <i class="far fa-calendar"></i>
                          {{ formatDate(post.date) }}
                        </span>
                        <span class="post-author">
                          <i class="far fa-user"></i>
                          {{ post.author }}
                        </span>
                      </div>
                      <h3 class="post-title">
                        <router-link :to="`/blog/${post.slug}`">{{ post.title }}</router-link>
                      </h3>
                      <p class="post-excerpt">{{ post.excerpt }}</p>
                      <div class="post-footer">
                        <router-link :to="`/blog/${post.slug}`" class="read-more">
                          Read More
                          <i class="fas fa-arrow-right"></i>
                        </router-link>
                        <div class="post-stats">
                          <span class="comments">
                            <i class="far fa-comment"></i>
                            {{ post.comments }}
                          </span>
                          <span class="likes">
                            <i class="far fa-heart"></i>
                            {{ post.likes }}
                          </span>
                        </div>
                      </div>
                    </div>
                  </article>
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
                  <li class="page-item" v-for="page in visiblePages" :key="page" 
                      :class="{ active: page === currentPage, disabled: page === '...' }">
                    <a class="page-link" href="#" @click.prevent="changePage(page)" v-if="page !== '...'">
                      {{ page }}
                    </a>
                    <span class="page-link" v-else>{{ page }}</span>
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
            <div class="blog-sidebar">
              <!-- Search Widget -->
              <div class="sidebar-widget">
                <h4 class="widget-title">Search Blog</h4>
                <div class="search-widget">
                  <form @submit.prevent="searchPosts">
                    <input type="text" v-model="searchQuery" placeholder="Search articles...">
                    <button type="submit"><i class="fas fa-search"></i></button>
                  </form>
                </div>
              </div>

              <!-- Categories Widget -->
              <div class="sidebar-widget">
                <h4 class="widget-title">Categories</h4>
                <div class="categories-widget">
                  <ul class="categories-list">
                    <li v-for="category in categories" :key="category.name">
                      <router-link to="#" :class="{ active: filterCategory === category.value }"
                                 @click="filterCategory = category.value">
                        {{ category.name }}
                        <span class="count">({{ category.count }})</span>
                      </router-link>
                    </li>
                  </ul>
                </div>
              </div>

              <!-- Popular Posts -->
              <div class="sidebar-widget">
                <h4 class="widget-title">Popular Posts</h4>
                <div class="popular-posts">
                  <div class="popular-post" v-for="post in popularPosts" :key="post.id">
                    <div class="post-thumb">
                      <router-link :to="`/blog/${post.slug}`">
                        <img :src="post.image" :alt="post.title">
                      </router-link>
                    </div>
                    <div class="post-info">
                      <h5>
                        <router-link :to="`/blog/${post.slug}`">{{ post.title }}</router-link>
                      </h5>
                      <div class="post-meta">
                        <span class="date">{{ formatDate(post.date) }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Tags Widget -->
              <div class="sidebar-widget">
                <h4 class="widget-title">Popular Tags</h4>
                <div class="tags-widget">
                  <div class="tags-list">
                    <a href="#" v-for="tag in popularTags" :key="tag" 
                       :class="{ active: selectedTags.includes(tag) }"
                       @click.prevent="toggleTag(tag)">
                      {{ tag }}
                    </a>
                  </div>
                </div>
              </div>

              <!-- Newsletter Widget -->
              <div class="sidebar-widget newsletter-widget">
                <h4 class="widget-title">Newsletter</h4>
                <div class="newsletter-content">
                  <p>Subscribe to get updates on new articles and learning resources</p>
                  <form @submit.prevent="subscribeNewsletter">
                    <input type="email" v-model="newsletterEmail" placeholder="Your email address" required>
                    <button type="submit" class="btn btn-primary">
                      <i class="fas fa-paper-plane"></i>
                      Subscribe
                    </button>
                  </form>
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
  name: 'BlogPage',
  data() {
    return {
      posts: [],
      searchQuery: '',
      filterCategory: '',
      selectedTags: [],
      currentPage: 1,
      itemsPerPage: 6,
      newsletterEmail: '',
      categories: [
        { name: 'Education', value: 'education', count: 15 },
        { name: 'Technology', value: 'technology', count: 12 },
        { name: 'Career', value: 'career', count: 8 },
        { name: 'Learning Tips', value: 'tips', count: 10 },
        { name: 'News & Updates', value: 'news', count: 5 }
      ],
      popularTags: [
        'Online Learning', 'EdTech', 'Study Tips', 'Career Growth', 
        'Programming', 'Mathematics', 'Science', 'Language Learning',
        'Professional Development', 'Student Success'
      ]
    }
  },
  computed: {
    filteredPosts() {
      let filtered = this.posts;

      // Filter by search query
      if (this.searchQuery) {
        filtered = filtered.filter(post => 
          post.title.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          post.excerpt.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
          post.content.toLowerCase().includes(this.searchQuery.toLowerCase())
        );
      }

      // Filter by category
      if (this.filterCategory) {
        filtered = filtered.filter(post => post.category === this.filterCategory);
      }

      // Filter by tags
      if (this.selectedTags.length > 0) {
        filtered = filtered.filter(post =>
          this.selectedTags.some(tag => post.tags.includes(tag))
        );
      }

      // Pagination
      const start = (this.currentPage - 1) * this.itemsPerPage;
      const end = start + this.itemsPerPage;
      return filtered.slice(start, end);
    },
    popularPosts() {
      return this.posts
        .sort((a, b) => b.views - a.views)
        .slice(0, 3);
    },
    totalPages() {
      return Math.ceil(this.posts.length / this.itemsPerPage);
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
    await this.fetchPosts();
  },
  watch: {
    searchQuery() {
      this.currentPage = 1;
    },
    filterCategory() {
      this.currentPage = 1;
    },
    selectedTags() {
      this.currentPage = 1;
    }
  },
  methods: {
    async fetchPosts() {
      try {
        // In a real application, you would fetch from your API
        // const response = await this.$http.get('/api/blog/posts');
        // this.posts = response.data.data;
        
        this.posts = this.getMockPosts();
      } catch (error) {
        console.error('Error fetching blog posts:', error);
        this.posts = this.getMockPosts();
      }
    },
    getMockPosts() {
      return [
        {
          id: 1,
          title: 'The Future of Online Education: Trends to Watch in 2024',
          slug: 'future-online-education-trends-2024',
          excerpt: 'Explore the latest trends shaping the future of online learning and how they will impact education worldwide.',
          content: 'Full article content...',
          image: '/assets/img/blog/post1.jpg',
          category: 'education',
          tags: ['Online Learning', 'EdTech', 'Future Trends'],
          author: 'Sarah Johnson',
          date: '2024-01-15',
          comments: 24,
          likes: 156,
          views: 1200
        },
        {
          id: 2,
          title: 'How to Stay Motivated While Learning Online',
          slug: 'stay-motivated-online-learning',
          excerpt: 'Practical tips and strategies to maintain motivation and achieve your learning goals in online courses.',
          content: 'Full article content...',
          image: '/assets/img/blog/post2.jpg',
          category: 'tips',
          tags: ['Study Tips', 'Motivation', 'Student Success'],
          author: 'Mike Chen',
          date: '2024-01-12',
          comments: 18,
          likes: 89,
          views: 980
        },
        {
          id: 3,
          title: 'The Role of AI in Personalized Learning',
          slug: 'ai-personalized-learning',
          excerpt: 'Discover how artificial intelligence is revolutionizing personalized education and adaptive learning systems.',
          content: 'Full article content...',
          image: '/assets/img/blog/post3.jpg',
          category: 'technology',
          tags: ['AI', 'EdTech', 'Personalized Learning'],
          author: 'Dr. Emily Roberts',
          date: '2024-01-08',
          comments: 32,
          likes: 210,
          views: 1500
        },
        {
          id: 4,
          title: 'Career Paths in the Digital Age: Skills That Matter',
          slug: 'career-paths-digital-age-skills',
          excerpt: 'Essential skills and career paths that are in high demand in today\'s rapidly evolving digital landscape.',
          content: 'Full article content...',
          image: '/assets/img/blog/post4.jpg',
          category: 'career',
          tags: ['Career Growth', 'Digital Skills', 'Professional Development'],
          author: 'David Thompson',
          date: '2024-01-05',
          comments: 15,
          likes: 76,
          views: 850
        },
        {
          id: 5,
          title: 'New Features: Enhanced Learning Dashboard Released',
          slug: 'new-features-enhanced-learning-dashboard',
          excerpt: 'Explore the latest updates to our learning platform with new features designed to enhance your educational experience.',
          content: 'Full article content...',
          image: '/assets/img/blog/post5.jpg',
          category: 'news',
          tags: ['Platform Updates', 'New Features', 'User Experience'],
          author: 'SkillGro Team',
          date: '2024-01-02',
          comments: 28,
          likes: 134,
          views: 1100
        },
        {
          id: 6,
          title: 'Mastering Mathematics: Effective Study Techniques',
          slug: 'mastering-mathematics-study-techniques',
          excerpt: 'Proven study techniques and approaches to help students excel in mathematics and build strong problem-solving skills.',
          content: 'Full article content...',
          image: '/assets/img/blog/post6.jpg',
          category: 'education',
          tags: ['Mathematics', 'Study Tips', 'Problem Solving'],
          author: 'Prof. James Wilson',
          date: '2023-12-28',
          comments: 21,
          likes: 98,
          views: 920
        }
      ];
    },
    formatDate(dateString) {
      const date = new Date(dateString);
      return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
      });
    },
    searchPosts() {
      // Search is handled by computed property
    },
    toggleTag(tag) {
      const index = this.selectedTags.indexOf(tag);
      if (index > -1) {
        this.selectedTags.splice(index, 1);
      } else {
        this.selectedTags.push(tag);
      }
    },
    changePage(page) {
      if (page >= 1 && page <= this.totalPages && page !== this.currentPage) {
        this.currentPage = page;
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }
    },
    subscribeNewsletter() {
      if (this.newsletterEmail) {
        console.log('Subscribing:', this.newsletterEmail);
        this.$toast.success('Thank you for subscribing to our newsletter!');
        this.newsletterEmail = '';
      }
    }
  }
}
</script>

<style scoped>
.blog-header {
  background: white;
  padding: 25px;
  border-radius: 10px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}

.blog-title {
  margin: 0;
  color: #2c3e50;
  font-size: 1.5rem;
  font-weight: 700;
}

.blog-title span {
  color: #7f8c8d;
  font-size: 1rem;
  font-weight: 400;
}

/* Blog Posts */
.blog-post {
  background: white;
  border-radius: 15px;
  overflow: hidden;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  margin-bottom: 30px;
  height: 100%;
  display: flex;
  flex-direction: column;
}

.blog-post:hover {
  transform: translateY(-10px);
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

.post-thumbnail {
  position: relative;
  height: 200px;
  overflow: hidden;
  flex-shrink: 0;
}

.post-thumbnail img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.blog-post:hover .post-thumbnail img {
  transform: scale(1.1);
}

.post-category {
  position: absolute;
  top: 15px;
  left: 15px;
  z-index: 2;
}

.post-category span {
  padding: 6px 12px;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
  color: white;
  text-transform: uppercase;
  display: inline-block;
}

.category-education { background: #3498db; }
.category-technology { background: #9b59b6; }
.category-career { background: #e74c3c; }
.category-tips { background: #2ecc71; }
.category-news { background: #f39c12; }

.post-content {
  padding: 25px;
  flex-grow: 1;
  display: flex;
  flex-direction: column;
}

.post-meta {
  display: flex;
  gap: 15px;
  margin-bottom: 15px;
  font-size: 0.9rem;
  color: #7f8c8d;
  flex-wrap: wrap;
}

.post-meta span {
  display: flex;
  align-items: center;
  gap: 5px;
}

.post-meta i {
  font-size: 0.8rem;
}

.post-title {
  margin-bottom: 15px;
  line-height: 1.4;
}

.post-title a {
  color: #2c3e50;
  text-decoration: none;
  font-size: 1.3rem;
  font-weight: 700;
  display: block;
  transition: color 0.3s ease;
}

.post-title a:hover {
  color: #e74c3c;
}

/* Fixed line-clamp with complete browser compatibility */
.post-excerpt {
  color: #7f8c8d;
  line-height: 1.6;
  margin-bottom: 20px;
  overflow: hidden;
  
  /* WebKit browsers (Chrome, Safari, newer Edge) */
  display: -webkit-box;
  -webkit-line-clamp: 3;
  -webkit-box-orient: vertical;
  
  /* Firefox */
  display: -moz-box;
  -moz-line-clamp: 3;
  -moz-box-orient: vertical;
  
  /* Standard property */
  display: box;
  line-clamp: 3;
  box-orient: vertical;
  
  /* Fallback for very old browsers */
  max-height: 4.8em;
  position: relative;
  flex-grow: 1;
}

/* Gradient fade fallback */
.post-excerpt::after {
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
@supports (-webkit-line-clamp: 3) or (line-clamp: 3) or (-moz-line-clamp: 3) {
  .post-excerpt::after {
    display: none;
  }
  
  .post-excerpt {
    max-height: none;
  }
}

.post-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 20px;
  border-top: 1px solid #f8f9fa;
  margin-top: auto;
}

.read-more {
  color: #e74c3c;
  text-decoration: none;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 5px;
  transition: all 0.3s ease;
  font-size: 0.9rem;
}

.read-more:hover {
  gap: 10px;
  color: #c0392b;
}

.read-more i {
  font-size: 0.8rem;
  transition: transform 0.3s ease;
}

.read-more:hover i {
  transform: translateX(3px);
}

.post-stats {
  display: flex;
  gap: 15px;
  color: #7f8c8d;
  font-size: 0.9rem;
}

.post-stats span {
  display: flex;
  align-items: center;
  gap: 5px;
}

.post-stats i {
  font-size: 0.8rem;
}

/* Sidebar */
.blog-sidebar {
  position: sticky;
  top: 100px;
}

.sidebar-widget {
  background: white;
  padding: 25px;
  border-radius: 10px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
  margin-bottom: 30px;
}

.widget-title {
  font-size: 1.3rem;
  color: #2c3e50;
  margin-bottom: 20px;
  padding-bottom: 15px;
  border-bottom: 2px solid #f8f9fa;
  font-weight: 700;
}

/* Search Widget */
.search-widget form {
  position: relative;
  display: flex;
}

.search-widget input {
  width: 100%;
  padding: 12px 45px 12px 15px;
  border: 2px solid #e9ecef;
  border-radius: 8px;
  font-size: 1rem;
  background: #f8f9fa;
  transition: all 0.3s ease;
}

.search-widget input:focus {
  outline: none;
  border-color: #e74c3c;
  background: white;
  box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
}

.search-widget button {
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

.search-widget button:hover {
  color: #e74c3c;
}

/* Categories Widget */
.categories-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.categories-list li {
  margin-bottom: 10px;
}

.categories-list li:last-child {
  margin-bottom: 0;
}

.categories-list a {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 0;
  color: #7f8c8d;
  text-decoration: none;
  border-bottom: 1px solid #f8f9fa;
  transition: all 0.3s ease;
  font-size: 0.95rem;
}

.categories-list a:hover,
.categories-list a.active {
  color: #e74c3c;
  padding-left: 10px;
}

.categories-list a.active {
  font-weight: 600;
}

.count {
  background: #f8f9fa;
  padding: 2px 8px;
  border-radius: 10px;
  font-size: 0.8rem;
  color: #7f8c8d;
}

/* Popular Posts */
.popular-posts {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.popular-post {
  display: flex;
  gap: 15px;
  align-items: flex-start;
}

.popular-post:last-child {
  margin-bottom: 0;
}

.post-thumb {
  width: 70px;
  height: 70px;
  border-radius: 8px;
  overflow: hidden;
  flex-shrink: 0;
}

.post-thumb img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.popular-post:hover .post-thumb img {
  transform: scale(1.1);
}

.post-info {
  flex: 1;
}

.post-info h5 {
  margin-bottom: 8px;
  line-height: 1.4;
  font-size: 0.95rem;
}

.post-info h5 a {
  color: #2c3e50;
  text-decoration: none;
  transition: color 0.3s ease;
  display: block;
}

.post-info h5 a:hover {
  color: #e74c3c;
}

.post-meta .date {
  font-size: 0.8rem;
  color: #7f8c8d;
}

/* Tags Widget */
.tags-list {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.tags-list a {
  background: #f8f9fa;
  color: #7f8c8d;
  padding: 6px 12px;
  border-radius: 20px;
  text-decoration: none;
  font-size: 0.85rem;
  transition: all 0.3s ease;
  border: 1px solid transparent;
}

.tags-list a:hover,
.tags-list a.active {
  background: #e74c3c;
  color: white;
  border-color: #e74c3c;
  transform: translateY(-2px);
}

/* Newsletter Widget */
.newsletter-widget {
  background: linear-gradient(135deg, #e74c3c, #c0392b);
  color: white;
  border: none;
}

.newsletter-widget .widget-title {
  color: white;
  border-bottom-color: rgba(255, 255, 255, 0.2);
}

.newsletter-content p {
  margin-bottom: 20px;
  opacity: 0.9;
  line-height: 1.5;
}

.newsletter-content form {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.newsletter-content input {
  padding: 12px 15px;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s ease;
}

.newsletter-content input:focus {
  outline: none;
  box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.3);
}

.newsletter-content .btn {
  justify-content: center;
  background: #2c3e50;
  border: none;
  padding: 12px 20px;
  font-weight: 600;
  transition: all 0.3s ease;
}

.newsletter-content .btn:hover {
  background: #34495e;
  transform: translateY(-2px);
}

/* Pagination */
.pagination-wrap {
  display: flex;
  justify-content: center;
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
}

.form-select:focus {
  outline: none;
  border-color: #e74c3c;
  background: white;
  box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
}

/* Blog Filter */
.blog-filter {
  display: flex;
  justify-content: flex-end;
}

/* Responsive Design */
@media (max-width: 768px) {
  .blog-header {
    text-align: center;
    padding: 20px;
  }
  
  .blog-header .row {
    gap: 15px;
  }
  
  .blog-filter {
    justify-content: center;
  }
  
  .post-footer {
    flex-direction: column;
    gap: 15px;
    align-items: flex-start;
  }
  
  .popular-post {
    flex-direction: column;
    text-align: center;
    gap: 10px;
  }
  
  .post-thumb {
    width: 100%;
    height: 150px;
    margin-bottom: 0;
  }
  
  .post-meta {
    flex-direction: column;
    gap: 8px;
  }
  
  .sidebar-widget {
    padding: 20px;
  }
  
  .pagination {
    justify-content: center;
  }
}

@media (max-width: 480px) {
  .blog-post {
    margin-bottom: 20px;
  }
  
  .post-content {
    padding: 20px;
  }
  
  .post-title a {
    font-size: 1.1rem;
  }
  
  .post-excerpt {
    font-size: 0.9rem;
  }
  
  .sidebar-widget {
    padding: 15px;
  }
  
  .tags-list {
    gap: 6px;
  }
  
  .tags-list a {
    padding: 5px 10px;
    font-size: 0.8rem;
  }
  
  .widget-title {
    font-size: 1.1rem;
  }
  
  .page-link {
    padding: 8px 12px;
    font-size: 0.9rem;
  }
}

/* Animation enhancements */
.blog-post {
  animation: fadeInUp 0.6s ease-out;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Focus styles for accessibility */
a:focus,
button:focus,
input:focus,
select:focus {
  outline: 2px solid #e74c3c;
  outline-offset: 2px;
}

/* High contrast mode support */
@media (prefers-contrast: high) {
  .blog-post {
    border: 2px solid #2c3e50;
  }
  
  .sidebar-widget {
    border: 1px solid #2c3e50;
  }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
  .blog-post,
  .blog-post:hover,
  .read-more,
  .tags-list a,
  .page-link,
  .form-select,
  .search-widget input,
  .newsletter-content .btn {
    transition: none;
  }
  
  .blog-post:hover {
    transform: none;
  }
}
</style>