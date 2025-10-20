<template>
  <footer class="footer__area footer__area-two">
    <div class="footer__top">
      <div class="container">
        <div class="row">
          <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="footer__widget">
              <div class="logo mb-35">
                <router-link to="/">
                  <img src="/assets/img/logo/secondary_logo.svg" alt="Logo">
                </router-link>
              </div>
              <div class="footer__content">
                <p>Transform your learning experience with our comprehensive online courses and expert instructors.</p>
                <ul class="list-wrap">
                  <li><i class="flaticon-map"></i>463 7th Ave, NY 10018, USA</li>
                  <li><i class="flaticon-phone"></i>+123 88 9900 456</li>
                  <li><i class="flaticon-email"></i>info@skillgro.com</li>
                </ul>
              </div>
            </div>
          </div>
          
          <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
            <div class="footer__widget">
              <h4 class="footer__widget-title">Quick Links</h4>
              <div class="footer__link">
                <ul class="list-wrap">
                  <li><router-link to="/about">About Us</router-link></li>
                  <li><router-link to="/courses">All Courses</router-link></li>
                  <li><router-link to="/instructors">Our Instructors</router-link></li>
                  <li><router-link to="/events">Events</router-link></li>
                  <li><router-link to="/blog">Blog</router-link></li>
                  <li><router-link to="/contact">Contact</router-link></li>
                </ul>
              </div>
            </div>
          </div>
          
          <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6">
            <div class="footer__widget">
              <h4 class="footer__widget-title">Categories</h4>
              <div class="footer__link">
                <ul class="list-wrap">
                  <li><router-link to="/courses?category=business">Business</router-link></li>
                  <li><router-link to="/courses?category=design">Design</router-link></li>
                  <li><router-link to="/courses?category=development">Development</router-link></li>
                  <li><router-link to="/courses?category=marketing">Marketing</router-link></li>
                  <li><router-link to="/courses?category=data-science">Data Science</router-link></li>
                </ul>
              </div>
            </div>
          </div>
          
          <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="footer__widget">
              <h4 class="footer__widget-title">Newsletter</h4>
              <div class="footer__contact-content">
                <p>Subscribe to get updates on new courses and events</p>
                <div class="newsletter__form">
                  <form @submit.prevent="subscribeNewsletter">
                    <input v-model="email" type="email" placeholder="Your email" required>
                    <button type="submit" class="btn">Subscribe</button>
                  </form>
                </div>
                <ul class="list-wrap footer__social">
                  <li><a href="#"><i class="fab fa-facebook-f"></i></a></li>
                  <li><a href="#"><i class="fab fa-twitter"></i></a></li>
                  <li><a href="#"><i class="fab fa-linkedin-in"></i></a></li>
                  <li><a href="#"><i class="fab fa-instagram"></i></a></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="footer__bottom">
      <div class="container">
        <div class="row align-items-center">
          <div class="col-md-6">
            <div class="copy-right-text">
              <p>&copy; {{ currentYear }} SkillGro. All rights reserved.</p>
            </div>
          </div>
          <div class="col-md-6">
            <div class="footer__bottom-menu">
              <ul class="list-wrap">
                <li><router-link to="/privacy">Privacy Policy</router-link></li>
                <li><router-link to="/terms">Terms of Service</router-link></li>
              </ul>
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