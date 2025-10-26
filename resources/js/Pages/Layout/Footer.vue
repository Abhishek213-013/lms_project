<template>
  <footer class="tg-footer" style="background-image: url(/assets/img/bg/footer_bg.png);">
    <div class="tg-footer__top">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <div class="tg-footer__top-inner">
              <div class="row">
                <!-- Footer Widget 1: About -->
                <div class="col-xl-4 col-lg-6 col-md-6">
                  <div class="tg-footer__widget">
                    <div class="tg-footer__widget-title">
                      <h4>About SkillGro</h4>
                    </div>
                    <div class="tg-footer__widget-content">
                      <div class="tg-footer__about">
                        <div class="logo">
                          <a href="/">
                            <img src="/assets/img/logo/logo.svg" alt="Logo">
                          </a>
                        </div>
                        <p>SkillGro is a comprehensive learning management system designed to provide quality education and skill development for students and professionals.</p>
                        <div class="tg-footer__social">
                          <a href="#"><i class="fab fa-facebook-f"></i></a>
                          <a href="#"><i class="fab fa-twitter"></i></a>
                          <a href="#"><i class="fab fa-linkedin-in"></i></a>
                          <a href="#"><i class="fab fa-instagram"></i></a>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Footer Widget 2: Quick Links -->
                <div class="col-xl-2 col-lg-3 col-md-3 col-sm-6">
                  <div class="tg-footer__widget">
                    <div class="tg-footer__widget-title">
                      <h4>Quick Links</h4>
                    </div>
                    <div class="tg-footer__widget-content">
                      <ul class="tg-footer__widget-links list-wrap">
                        <li><a href="/">Home</a></li>
                        <li><a href="/about">About Us</a></li>
                        <li><a href="/courses">Courses</a></li>
                        <li><a href="/instructors">Instructors</a></li>
                        <li><a href="/contact">Contact</a></li>
                      </ul>
                    </div>
                  </div>
                </div>

                <!-- Footer Widget 3: Courses -->
                <div class="col-xl-3 col-lg-3 col-md-3 col-sm-6">
                  <div class="tg-footer__widget">
                    <div class="tg-footer__widget-title">
                      <h4>Popular Courses</h4>
                    </div>
                    <div class="tg-footer__widget-content">
                      <ul class="tg-footer__widget-links list-wrap">
                        <li><a href="/courses">Web Development</a></li>
                        <li><a href="/courses">Data Science</a></li>
                        <li><a href="/courses">Digital Marketing</a></li>
                        <li><a href="/courses">Graphic Design</a></li>
                        <li><a href="/courses">Business Management</a></li>
                      </ul>
                    </div>
                  </div>
                </div>

                <!-- Footer Widget 4: Contact -->
                <div class="col-xl-3 col-lg-6 col-md-6">
                  <div class="tg-footer__widget">
                    <div class="tg-footer__widget-title">
                      <h4>Contact Info</h4>
                    </div>
                    <div class="tg-footer__widget-content">
                      <div class="tg-footer__contact">
                        <ul class="list-wrap">
                          <li>
                            <div class="icon">
                              <i class="flaticon-phone-call"></i>
                            </div>
                            <div class="content">
                              <p>Call Us</p>
                              <a href="tel:+11234567890">+1 (123) 456-7890</a>
                            </div>
                          </li>
                          <li>
                            <div class="icon">
                              <i class="flaticon-email"></i>
                            </div>
                            <div class="content">
                              <p>Email Us</p>
                              <a href="mailto:info@skillgro.com">info@skillgro.com</a>
                            </div>
                          </li>
                          <li>
                            <div class="icon">
                              <i class="flaticon-pin"></i>
                            </div>
                            <div class="content">
                              <p>Visit Us</p>
                              <span>123 Education Street, Learning City</span>
                            </div>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer Bottom -->
    <div class="tg-footer__bottom">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <div class="tg-footer__bottom-inner">
              <div class="row align-items-center">
                <div class="col-lg-6 col-md-7">
                  <div class="tg-footer__copyright">
                    <p>&copy; 2024 SkillGro. All Rights Reserved.</p>
                  </div>
                </div>
                <div class="col-lg-6 col-md-5">
                  <div class="tg-footer__bottom-links">
                    <ul class="list-wrap">
                      <li><a href="/privacy">Privacy Policy</a></li>
                      <li><a href="/terms">Terms of Service</a></li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<script>
export default {
  name: 'AppFooter',
  data() {
    return {
      email: '',
      currentYear: new Date().getFullYear()
    }
  },
  methods: {
    subscribeNewsletter() {
      // Implement newsletter subscription
      console.log('Subscribing:', this.email);
      this.email = '';
    }
  }
}
</script>

<style scoped>
/* Add any custom footer styles here if needed */
.tg-footer {
  position: relative;
  background-size: cover;
  background-position: center;
  color: white;
}

.tg-footer__social a {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  background: rgba(255, 255, 255, 0.1);
  border-radius: 50%;
  margin-right: 10px;
  transition: all 0.3s ease;
}

.tg-footer__social a:hover {
  background: #4f46e5;
  transform: translateY(-2px);
}

.tg-footer__widget-links li {
  margin-bottom: 10px;
}

.tg-footer__widget-links a {
  color: rgba(255, 255, 255, 0.8);
  transition: color 0.3s ease;
}

.tg-footer__widget-links a:hover {
  color: #4f46e5;
}

.tg-footer__contact li {
  display: flex;
  align-items: flex-start;
  margin-bottom: 20px;
}

.tg-footer__contact .icon {
  margin-right: 15px;
  color: #4f46e5;
}

.tg-footer__contact .content p {
  margin: 0;
  font-size: 14px;
  opacity: 0.8;
}

.tg-footer__contact .content a,
.tg-footer__contact .content span {
  color: white;
  text-decoration: none;
}

.tg-footer__bottom {
  border-top: 1px solid rgba(255, 255, 255, 0.1);
  padding: 20px 0;
}

.tg-footer__bottom-links ul {
  display: flex;
  justify-content: flex-end;
  gap: 20px;
}

@media (max-width: 768px) {
  .tg-footer__bottom-links ul {
    justify-content: center;
    margin-top: 15px;
  }
  
  .tg-footer__copyright {
    text-align: center;
  }
}
</style>