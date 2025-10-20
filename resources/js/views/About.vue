<template>
  <div class="main-area fix">
    <!-- Page Banner -->
    <section class="banner-area-two banner-bg-two" style="background-image: url('/assets/img/banner/banner_bg02.png')">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-xl-8 col-lg-10">
            <div class="banner__content-two text-center">
              <h3 class="title">About SkillGro</h3>
              <p>Empowering learners worldwide with quality education and innovative learning solutions</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- About Content Section -->
    <section class="about-content-area section-py-120">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-lg-6">
            <div class="about-content">
              <div class="section__title mb-30">
                <span class="sub-title">Our Story</span>
                <h2 class="title">Transforming Education Through Innovation</h2>
              </div>
              <p class="about-description">
                SkillGro was founded with a simple yet powerful vision: to make quality education accessible to everyone, everywhere. 
                We believe that learning should be engaging, personalized, and available to all regardless of geographical or financial barriers.
              </p>
              <p>
                Our platform brings together expert instructors, cutting-edge technology, and a supportive community to create 
                an exceptional learning experience. From primary education to professional skill development, we're committed to 
                helping learners achieve their full potential.
              </p>
              
              <div class="about-features">
                <div class="feature-item">
                  <div class="feature-icon">
                    <i class="flaticon-innovation"></i>
                  </div>
                  <div class="feature-content">
                    <h4>Innovative Learning</h4>
                    <p>Interactive courses with modern teaching methodologies</p>
                  </div>
                </div>
                <div class="feature-item">
                  <div class="feature-icon">
                    <i class="flaticon-expert"></i>
                  </div>
                  <div class="feature-content">
                    <h4>Expert Instructors</h4>
                    <p>Learn from industry professionals and experienced educators</p>
                  </div>
                </div>
                <div class="feature-item">
                  <div class="feature-icon">
                    <i class="flaticon-support-1"></i>
                  </div>
                  <div class="feature-content">
                    <h4>24/7 Support</h4>
                    <p>Comprehensive support system for all our learners</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="about-image">
              <img src="/assets/img/about/about-main.jpg" alt="About SkillGro">
              <div class="about-experience">
                <div class="experience-content">
                  <h3>5+</h3>
                  <p>Years of Excellence</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Stats Section -->
    <section class="about-stats-area section-pb-120">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <div class="stats-grid">
              <div class="stat-item">
                <div class="stat-number">
                  <span class="counter" data-count="50000">0</span>+
                </div>
                <div class="stat-label">Students Enrolled</div>
              </div>
              <div class="stat-item">
                <div class="stat-number">
                  <span class="counter" data-count="200">0</span>+
                </div>
                <div class="stat-label">Expert Instructors</div>
              </div>
              <div class="stat-item">
                <div class="stat-number">
                  <span class="counter" data-count="150">0</span>+
                </div>
                <div class="stat-label">Courses Available</div>
              </div>
              <div class="stat-item">
                <div class="stat-number">
                  <span class="counter" data-count="95">0</span>%
                </div>
                <div class="stat-label">Success Rate</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Mission & Vision -->
    <section class="mission-vision-area section-pb-120">
      <div class="container">
        <div class="row">
          <div class="col-lg-6">
            <div class="mission-card">
              <div class="card-icon">
                <i class="flaticon-target"></i>
              </div>
              <h3>Our Mission</h3>
              <p>
                To democratize education by providing high-quality, accessible, and affordable learning opportunities 
                that empower individuals to achieve their personal and professional goals.
              </p>
              <ul class="mission-list">
                <li><i class="fas fa-check"></i> Provide accessible education for all</li>
                <li><i class="fas fa-check"></i> Foster lifelong learning habits</li>
                <li><i class="fas fa-check"></i> Bridge the skills gap in the workforce</li>
                <li><i class="fas fa-check"></i> Promote innovation in education</li>
              </ul>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="vision-card">
              <div class="card-icon">
                <i class="flaticon-vision"></i>
              </div>
              <h3>Our Vision</h3>
              <p>
                To create a world where anyone, anywhere can transform their life through access to the world's best 
                learning experiences and educational resources.
              </p>
              <ul class="vision-list">
                <li><i class="fas fa-check"></i> Global learning community</li>
                <li><i class="fas fa-check"></i> Personalized learning paths</li>
                <li><i class="fas fa-check"></i> Industry-relevant curriculum</li>
                <li><i class="fas fa-check"></i> Continuous innovation in EdTech</li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Team Section -->
    <section class="team-area section-pb-120">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <div class="section__title text-center mb-50">
              <span class="sub-title">Leadership Team</span>
              <h2 class="title">Meet Our Founders</h2>
              <p>The passionate individuals behind SkillGro's success story</p>
            </div>
          </div>
        </div>
        <div class="row justify-content-center">
          <div class="col-xl-3 col-lg-4 col-md-6" v-for="member in teamMembers" :key="member.id">
            <div class="team-member">
              <div class="member-image">
                <img :src="member.image" :alt="member.name">
                <div class="member-overlay">
                  <div class="social-links">
                    <a :href="member.social.linkedin"><i class="fab fa-linkedin-in"></i></a>
                    <a :href="member.social.twitter"><i class="fab fa-twitter"></i></a>
                    <a :href="member.social.email"><i class="fas fa-envelope"></i></a>
                  </div>
                </div>
              </div>
              <div class="member-info">
                <h4 class="member-name">{{ member.name }}</h4>
                <p class="member-role">{{ member.role }}</p>
                <p class="member-bio">{{ member.bio }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Values Section -->
    <section class="values-area section-pb-120" style="background-color: #f8f9fa;">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <div class="section__title text-center mb-50">
              <span class="sub-title">Our Values</span>
              <h2 class="title">What We Stand For</h2>
            </div>
          </div>
        </div>
        <div class="row justify-content-center">
          <div class="col-xl-4 col-lg-6" v-for="value in values" :key="value.id">
            <div class="value-card">
              <div class="value-icon">
                <i :class="value.icon"></i>
              </div>
              <h4>{{ value.title }}</h4>
              <p>{{ value.description }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
export default {
  name: 'AboutPage',
  data() {
    return {
      teamMembers: [
        {
          id: 1,
          name: 'Dr. Sarah Mitchell',
          role: 'CEO & Founder',
          image: '/assets/img/team/ceo.jpg',
          bio: 'Education technology expert with 15+ years in the industry. Passionate about making quality education accessible to all.',
          social: {
            linkedin: '#',
            twitter: '#',
            email: '#'
          }
        },
        {
          id: 2,
          name: 'Michael Zhang',
          role: 'CTO & Co-founder',
          image: '/assets/img/team/cto.jpg',
          bio: 'Tech innovator and software architect dedicated to building scalable learning platforms that enhance educational experiences.',
          social: {
            linkedin: '#',
            twitter: '#',
            email: '#'
          }
        },
        {
          id: 3,
          name: 'Emily Rodriguez',
          role: 'Head of Education',
          image: '/assets/img/team/education-head.jpg',
          bio: 'Curriculum development specialist with a PhD in Education. Focused on creating engaging and effective learning materials.',
          social: {
            linkedin: '#',
            twitter: '#',
            email: '#'
          }
        },
        {
          id: 4,
          name: 'David Thompson',
          role: 'Operations Director',
          image: '/assets/img/team/operations.jpg',
          bio: 'Operations management expert ensuring smooth delivery of our educational services and excellent student support.',
          social: {
            linkedin: '#',
            twitter: '#',
            email: '#'
          }
        }
      ],
      values: [
        {
          id: 1,
          title: 'Excellence',
          icon: 'flaticon-quality',
          description: 'We strive for the highest standards in everything we do, from course content to student support.'
        },
        {
          id: 2,
          title: 'Innovation',
          icon: 'flaticon-innovation',
          description: 'Constantly evolving our platform and teaching methods to provide the best learning experience.'
        },
        {
          id: 3,
          title: 'Accessibility',
          icon: 'flaticon-accessibility',
          description: 'Making quality education available to everyone, regardless of background or location.'
        },
        {
          id: 4,
          title: 'Community',
          icon: 'flaticon-community',
          description: 'Building a supportive learning community where students and instructors grow together.'
        },
        {
          id: 5,
          title: 'Integrity',
          icon: 'flaticon-integrity',
          description: 'Maintaining transparency, honesty, and ethical practices in all our operations.'
        },
        {
          id: 6,
          title: 'Impact',
          icon: 'flaticon-impact',
          description: 'Measuring our success by the positive impact we create in our students lives and careers.'
        }
      ]
    }
  },
  mounted() {
    this.animateCounters();
  },
  methods: {
    animateCounters() {
      const counters = document.querySelectorAll('.counter');
      const speed = 200;

      counters.forEach(counter => {
        const updateCount = () => {
          const target = +counter.getAttribute('data-count');
          const count = +counter.innerText;
          const increment = target / speed;

          if (count < target) {
            counter.innerText = Math.ceil(count + increment);
            setTimeout(updateCount, 1);
          } else {
            counter.innerText = target;
          }
        };

        updateCount();
      });
    }
  }
}
</script>

<style scoped>
.about-content-area {
  background: white;
}

.about-description {
  font-size: 1.1rem;
  line-height: 1.8;
  color: #555;
  margin-bottom: 30px;
}

.about-features {
  margin-top: 40px;
}

.feature-item {
  display: flex;
  align-items: flex-start;
  gap: 20px;
  margin-bottom: 30px;
}

.feature-icon {
  width: 60px;
  height: 60px;
  background: linear-gradient(135deg, #e74c3c, #c0392b);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.feature-icon i {
  font-size: 1.5rem;
  color: white;
}

.feature-content h4 {
  font-size: 1.3rem;
  color: #2c3e50;
  margin-bottom: 10px;
}

.feature-content p {
  color: #7f8c8d;
  margin: 0;
  line-height: 1.6;
}

.about-image {
  position: relative;
  text-align: center;
}

.about-image img {
  max-width: 100%;
  border-radius: 15px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
}

.about-experience {
  position: absolute;
  bottom: -30px;
  right: -30px;
  background: #e74c3c;
  color: white;
  padding: 30px;
  border-radius: 15px;
  text-align: center;
  box-shadow: 0 15px 30px rgba(231, 76, 60, 0.3);
}

.experience-content h3 {
  font-size: 3rem;
  font-weight: 700;
  margin: 0;
}

.experience-content p {
  margin: 0;
  font-weight: 600;
}

/* Stats Section */
.about-stats-area {
  background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
  color: white;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 30px;
}

.stat-item {
  text-align: center;
  padding: 40px 20px;
}

.stat-number {
  font-size: 3.5rem;
  font-weight: 700;
  color: #e74c3c;
  margin-bottom: 10px;
}

.stat-label {
  font-size: 1.2rem;
  color: #ecf0f1;
  font-weight: 600;
}

/* Mission & Vision */
.mission-card,
.vision-card {
  background: white;
  padding: 40px;
  border-radius: 15px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  height: 100%;
  position: relative;
  overflow: hidden;
}

.mission-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 5px;
  background: linear-gradient(135deg, #e74c3c, #c0392b);
}

.vision-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 5px;
  background: linear-gradient(135deg, #3498db, #2980b9);
}

.card-icon {
  width: 80px;
  height: 80px;
  background: #f8f9fa;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 25px;
}

.card-icon i {
  font-size: 2rem;
  color: #e74c3c;
}

.vision-card .card-icon i {
  color: #3498db;
}

.mission-card h3,
.vision-card h3 {
  font-size: 1.8rem;
  color: #2c3e50;
  margin-bottom: 20px;
}

.mission-card p,
.vision-card p {
  color: #7f8c8d;
  line-height: 1.7;
  margin-bottom: 25px;
}

.mission-list,
.vision-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.mission-list li,
.vision-list li {
  padding: 8px 0;
  display: flex;
  align-items: center;
  gap: 10px;
  color: #555;
}

.mission-list li i,
.vision-list li i {
  color: #27ae60;
}

/* Team Section */
.team-member {
  background: white;
  border-radius: 15px;
  overflow: hidden;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease;
  margin-bottom: 30px;
}

.team-member:hover {
  transform: translateY(-10px);
}

.member-image {
  position: relative;
  height: 300px;
  overflow: hidden;
}

.member-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.3s ease;
}

.team-member:hover .member-image img {
  transform: scale(1.1);
}

.member-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(231, 76, 60, 0.9);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.3s ease;
}

.team-member:hover .member-overlay {
  opacity: 1;
}

.social-links {
  display: flex;
  gap: 15px;
}

.social-links a {
  width: 45px;
  height: 45px;
  background: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #e74c3c;
  text-decoration: none;
  transition: all 0.3s ease;
}

.social-links a:hover {
  background: #2c3e50;
  color: white;
  transform: translateY(-3px);
}

.member-info {
  padding: 25px;
  text-align: center;
}

.member-name {
  font-size: 1.3rem;
  font-weight: 700;
  color: #2c3e50;
  margin-bottom: 5px;
}

.member-role {
  color: #e74c3c;
  font-weight: 600;
  margin-bottom: 15px;
}

.member-bio {
  color: #7f8c8d;
  line-height: 1.6;
  margin: 0;
}

/* Values Section */
.value-card {
  background: white;
  padding: 40px 30px;
  border-radius: 15px;
  text-align: center;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease;
  margin-bottom: 30px;
  height: 100%;
}

.value-card:hover {
  transform: translateY(-10px);
}

.value-icon {
  width: 80px;
  height: 80px;
  background: linear-gradient(135deg, #e74c3c, #c0392b);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto 25px;
}

.value-icon i {
  font-size: 2rem;
  color: white;
}

.value-card h4 {
  font-size: 1.5rem;
  color: #2c3e50;
  margin-bottom: 15px;
}

.value-card p {
  color: #7f8c8d;
  line-height: 1.7;
  margin: 0;
}

@media (max-width: 768px) {
  .about-experience {
    position: relative;
    bottom: auto;
    right: auto;
    margin-top: 30px;
  }
  
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .feature-item {
    flex-direction: column;
    text-align: center;
  }
}
</style>