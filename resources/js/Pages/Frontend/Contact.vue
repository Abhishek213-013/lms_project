<template>
  <div class="main-area fix">
    <!-- Page Banner -->
    <section class="banner-area-two banner-bg-two" style="background-image: url('/assets/img/banner/banner_bg02.png')">
      <div class="container">
        <div class="row justify-content-center">
          <div class="col-xl-8 col-lg-10">
            <div class="banner__content-two text-center">
              <h3 class="title">Get In Touch</h3>
              <p>We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-area section-py-120">
      <div class="container">
        <div class="row">
          <div class="col-lg-8">
            <div class="contact-form-wrapper">
              <div class="section__title mb-40">
                <span class="sub-title">Send Message</span>
                <h2 class="title">Let's Start a Conversation</h2>
              </div>

              <form @submit.prevent="submitForm" class="contact-form">
                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="firstName">First Name *</label>
                      <input type="text" id="firstName" v-model="form.firstName" required>
                      <span class="error" v-if="errors.firstName">{{ errors.firstName }}</span>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="lastName">Last Name *</label>
                      <input type="text" id="lastName" v-model="form.lastName" required>
                      <span class="error" v-if="errors.lastName">{{ errors.lastName }}</span>
                    </div>
                  </div>
                </div>

                <div class="row">
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="email">Email Address *</label>
                      <input type="email" id="email" v-model="form.email" required>
                      <span class="error" v-if="errors.email">{{ errors.email }}</span>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div class="form-group">
                      <label for="phone">Phone Number</label>
                      <input type="tel" id="phone" v-model="form.phone">
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <label for="subject">Subject *</label>
                  <select id="subject" v-model="form.subject" required>
                    <option value="">Select a subject</option>
                    <option value="general">General Inquiry</option>
                    <option value="admission">Admission Information</option>
                    <option value="technical">Technical Support</option>
                    <option value="billing">Billing Issue</option>
                    <option value="feedback">Feedback & Suggestions</option>
                    <option value="partnership">Partnership Opportunity</option>
                  </select>
                  <span class="error" v-if="errors.subject">{{ errors.subject }}</span>
                </div>

                <div class="form-group">
                  <label for="message">Message *</label>
                  <textarea id="message" v-model="form.message" rows="6" placeholder="Tell us how we can help you..." required></textarea>
                  <span class="error" v-if="errors.message">{{ errors.message }}</span>
                </div>

                <div class="form-submit">
                  <button type="submit" class="btn btn-primary" :disabled="loading">
                    <span v-if="loading">
                      <i class="fas fa-spinner fa-spin"></i> Sending...
                    </span>
                    <span v-else>
                      Send Message
                    </span>
                  </button>
                </div>
              </form>
            </div>
          </div>

          <div class="col-lg-4">
            <div class="contact-info-wrapper">
              <!-- Contact Information -->
              <div class="contact-info-card">
                <h4>Contact Information</h4>
                <div class="contact-info">
                  <div class="info-item">
                    <div class="info-icon">
                      <i class="flaticon-map"></i>
                    </div>
                    <div class="info-content">
                      <h5>Our Location</h5>
                      <p>123 Education Street<br>Learning City, LC 12345</p>
                    </div>
                  </div>
                  <div class="info-item">
                    <div class="info-icon">
                      <i class="flaticon-phone"></i>
                    </div>
                    <div class="info-content">
                      <h5>Phone Number</h5>
                      <p>+1 (555) 123-4567<br>+1 (555) 987-6543</p>
                    </div>
                  </div>
                  <div class="info-item">
                    <div class="info-icon">
                      <i class="flaticon-email"></i>
                    </div>
                    <div class="info-content">
                      <h5>Email Address</h5>
                      <p>info@skillgro.com<br>support@skillgro.com</p>
                    </div>
                  </div>
                  <div class="info-item">
                    <div class="info-icon">
                      <i class="flaticon-clock"></i>
                    </div>
                    <div class="info-content">
                      <h5>Working Hours</h5>
                      <p>Monday - Friday: 9:00 - 18:00<br>Saturday: 10:00 - 16:00</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Quick Support -->
              <div class="quick-support-card">
                <h4>Quick Support</h4>
                <p>Need immediate assistance? Check our frequently asked questions or contact our support team.</p>
                <div class="support-actions">
                  <router-link to="/faq" class="btn btn-outline">
                    <i class="fas fa-question-circle"></i>
                    FAQ
                  </router-link>
                  <a href="mailto:support@skillgro.com" class="btn btn-primary">
                    <i class="fas fa-headset"></i>
                    Live Chat
                  </a>
                </div>
              </div>

              <!-- Social Links -->
              <div class="social-links-card">
                <h4>Follow Us</h4>
                <div class="social-links">
                  <a href="#" class="social-link facebook">
                    <i class="fab fa-facebook-f"></i>
                  </a>
                  <a href="#" class="social-link twitter">
                    <i class="fab fa-twitter"></i>
                  </a>
                  <a href="#" class="social-link linkedin">
                    <i class="fab fa-linkedin-in"></i>
                  </a>
                  <a href="#" class="social-link instagram">
                    <i class="fab fa-instagram"></i>
                  </a>
                  <a href="#" class="social-link youtube">
                    <i class="fab fa-youtube"></i>
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Map Section -->
    <section class="map-area">
      <div class="container-fluid p-0">
        <div class="contact-map">
          <div class="map-placeholder">
            <div class="map-content">
              <i class="fas fa-map-marker-alt"></i>
              <h4>Our Location</h4>
              <p>123 Education Street, Learning City</p>
              <button class="btn btn-primary" @click="openMap">
                <i class="fas fa-external-link-alt"></i>
                Open in Google Maps
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-area section-pb-120">
      <div class="container">
        <div class="row">
          <div class="col-12">
            <div class="section__title text-center mb-50">
              <span class="sub-title">Common Questions</span>
              <h2 class="title">Frequently Asked Questions</h2>
            </div>
          </div>
        </div>
        <div class="row justify-content-center">
          <div class="col-lg-8">
            <div class="faq-accordion">
              <div class="accordion" id="faqAccordion">
                <div class="accordion-item" v-for="(faq, index) in faqs" :key="faq.id">
                  <h2 class="accordion-header">
                    <button class="accordion-button" :class="{ collapsed: index !== 0 }" 
                            type="button" data-bs-toggle="collapse" :data-bs-target="`#faq${faq.id}`">
                      {{ faq.question }}
                    </button>
                  </h2>
                  <div :id="`faq${faq.id}`" class="accordion-collapse collapse" :class="{ show: index === 0 }"
                       data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                      {{ faq.answer }}
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
  name: 'ContactPage',
  data() {
    return {
      form: {
        firstName: '',
        lastName: '',
        email: '',
        phone: '',
        subject: '',
        message: ''
      },
      errors: {},
      loading: false,
      faqs: [
        {
          id: 1,
          question: 'How do I enroll in a course?',
          answer: 'You can enroll in any course by visiting the course page and clicking the "Enroll Now" button. For free courses, you can enroll immediately. For paid courses, you will be guided through the payment process.'
        },
        {
          id: 2,
          question: 'What payment methods do you accept?',
          answer: 'We accept all major credit cards (Visa, MasterCard, American Express), PayPal, and bank transfers for certain regions. All payments are processed securely through our payment partners.'
        },
        {
          id: 3,
          question: 'Can I get a refund if I am not satisfied?',
          answer: 'Yes, we offer a 30-day money-back guarantee for all courses. If you are not satisfied with your purchase, you can request a full refund within 30 days of enrollment.'
        },
        {
          id: 4,
          question: 'Do you offer certificates upon completion?',
          answer: 'Yes, all our courses offer a certificate of completion that you can download and share. Certificates are issued automatically when you complete all course requirements.'
        },
        {
          id: 5,
          question: 'How can I contact my instructor?',
          answer: 'You can contact your instructor directly through the course messaging system. Most instructors respond within 24-48 hours. You can also participate in course discussions and Q&A sessions.'
        }
      ]
    }
  },
  methods: {
    validateForm() {
      this.errors = {};

      if (!this.form.firstName.trim()) {
        this.errors.firstName = 'First name is required';
      }

      if (!this.form.lastName.trim()) {
        this.errors.lastName = 'Last name is required';
      }

      if (!this.form.email.trim()) {
        this.errors.email = 'Email is required';
      } else if (!this.isValidEmail(this.form.email)) {
        this.errors.email = 'Please enter a valid email address';
      }

      if (!this.form.subject) {
        this.errors.subject = 'Please select a subject';
      }

      if (!this.form.message.trim()) {
        this.errors.message = 'Message is required';
      } else if (this.form.message.trim().length < 10) {
        this.errors.message = 'Message must be at least 10 characters long';
      }

      return Object.keys(this.errors).length === 0;
    },
    isValidEmail(email) {
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      return emailRegex.test(email);
    },
    async submitForm() {
      if (!this.validateForm()) {
        return;
      }

      this.loading = true;

      try {
        // Simulate API call
        await new Promise(resolve => setTimeout(resolve, 2000));
        
        // In a real application, you would send the form data to your backend
        console.log('Form submitted:', this.form);
        
        this.$toast.success('Thank you for your message! We will get back to you soon.');
        
        // Reset form
        this.form = {
          firstName: '',
          lastName: '',
          email: '',
          phone: '',
          subject: '',
          message: ''
        };
      } catch (error) {
        this.$toast.error('Sorry, there was an error sending your message. Please try again.');
        console.error('Error submitting form:', error);
      } finally {
        this.loading = false;
      }
    },
    openMap() {
      const address = encodeURIComponent('123 Education Street, Learning City, LC 12345');
      window.open(`https://www.google.com/maps/search/?api=1&query=${address}`, '_blank');
    }
  }
}
</script>

<style scoped>
.contact-form-wrapper {
  background: white;
  padding: 40px;
  border-radius: 15px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.contact-info-wrapper {
  position: sticky;
  top: 100px;
}

.contact-info-card,
.quick-support-card,
.social-links-card {
  background: white;
  padding: 30px;
  border-radius: 15px;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  margin-bottom: 30px;
}

.contact-info-card h4,
.quick-support-card h4,
.social-links-card h4 {
  font-size: 1.5rem;
  color: #2c3e50;
  margin-bottom: 25px;
  padding-bottom: 15px;
  border-bottom: 2px solid #f8f9fa;
  font-weight: 700;
}

/* Form Styles */
.form-group {
  margin-bottom: 25px;
}

.form-group label {
  display: block;
  margin-bottom: 8px;
  font-weight: 600;
  color: #2c3e50;
}

.form-group input,
.form-group select,
.form-group textarea {
  width: 100%;
  padding: 12px 15px;
  border: 2px solid #e9ecef;
  border-radius: 8px;
  font-size: 1rem;
  transition: all 0.3s ease;
  background: #f8f9fa;
  font-family: inherit;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #e74c3c;
  background: white;
  box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
}

.form-group textarea {
  resize: vertical;
  min-height: 120px;
}

.form-group select {
  cursor: pointer;
  appearance: none;
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
  background-position: right 12px center;
  background-repeat: no-repeat;
  background-size: 16px;
  padding-right: 40px;
}

.error {
  color: #e74c3c;
  font-size: 0.9rem;
  margin-top: 5px;
  display: block;
  font-weight: 500;
}

.form-submit {
  margin-top: 30px;
  text-align: center;
}

.btn {
  padding: 12px 30px;
  border-radius: 8px;
  font-weight: 600;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  border: none;
  cursor: pointer;
  transition: all 0.3s ease;
  font-size: 1rem;
  justify-content: center;
  min-height: 48px;
}

.btn-primary {
  background: #e74c3c;
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background: #c0392b;
  transform: translateY(-2px);
  box-shadow: 0 10px 20px rgba(231, 76, 60, 0.3);
}

.btn-primary:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

.btn-outline {
  background: transparent;
  color: #e74c3c;
  border: 2px solid #e74c3c;
}

.btn-outline:hover {
  background: #e74c3c;
  color: white;
  transform: translateY(-2px);
}

/* Contact Info Styles */
.contact-info {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.info-item {
  display: flex;
  align-items: flex-start;
  gap: 15px;
  padding: 15px 0;
}

.info-item:not(:last-child) {
  border-bottom: 1px solid #f8f9fa;
}

.info-icon {
  width: 50px;
  height: 50px;
  background: #f8f9fa;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  transition: all 0.3s ease;
}

.info-item:hover .info-icon {
  background: #e74c3c;
  transform: scale(1.1);
}

.info-item:hover .info-icon i {
  color: white;
}

.info-icon i {
  font-size: 1.2rem;
  color: #e74c3c;
  transition: all 0.3s ease;
}

.info-content h5 {
  font-size: 1.1rem;
  color: #2c3e50;
  margin-bottom: 5px;
  font-weight: 600;
}

.info-content p {
  color: #7f8c8d;
  margin: 0;
  line-height: 1.5;
  font-size: 0.95rem;
}

/* Quick Support */
.quick-support-card p {
  color: #7f8c8d;
  margin-bottom: 20px;
  line-height: 1.6;
  font-size: 0.95rem;
}

.support-actions {
  display: flex;
  gap: 10px;
  flex-wrap: wrap;
}

.support-actions .btn {
  flex: 1;
  min-width: 120px;
  justify-content: center;
  text-align: center;
}

/* Social Links */
.social-links {
  display: flex;
  gap: 10px;
  justify-content: center;
  flex-wrap: wrap;
}

.social-link {
  width: 45px;
  height: 45px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  text-decoration: none;
  transition: all 0.3s ease;
  font-size: 1.1rem;
}

.social-link:hover {
  transform: translateY(-3px) scale(1.1);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
}

.facebook { background: #3b5998; }
.twitter { background: #1da1f2; }
.linkedin { background: #0077b5; }
.instagram { background: #e4405f; }
.youtube { background: #cd201f; }

/* Map Styles */
.map-area {
  margin-top: 80px;
}

.contact-map {
  height: 400px;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  position: relative;
  display: flex;
  align-items: center;
  justify-content: center;
}

.map-placeholder {
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  text-align: center;
  width: 100%;
}

.map-content i {
  font-size: 3rem;
  margin-bottom: 20px;
  opacity: 0.8;
  display: block;
}

.map-content h4 {
  font-size: 1.8rem;
  margin-bottom: 10px;
  font-weight: 700;
}

.map-content p {
  font-size: 1.1rem;
  margin-bottom: 25px;
  opacity: 0.9;
  line-height: 1.5;
}

.map-content .btn {
  margin-top: 10px;
}

/* FAQ Styles */
.faq-accordion {
  background: white;
  border-radius: 15px;
  overflow: hidden;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.accordion-item {
  border: none;
  border-bottom: 1px solid #e9ecef;
  background: white;
}

.accordion-item:last-child {
  border-bottom: none;
}

.accordion-button {
  padding: 20px 25px;
  font-weight: 600;
  color: #2c3e50;
  background: white;
  border: none;
  box-shadow: none;
  font-size: 1.1rem;
  transition: all 0.3s ease;
}

.accordion-button:not(.collapsed) {
  background: #f8f9fa;
  color: #e74c3c;
  box-shadow: none;
}

.accordion-button:hover {
  background: #f8f9fa;
  color: #e74c3c;
}

.accordion-button::after {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%232c3e50'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
  transition: transform 0.3s ease;
}

.accordion-button:not(.collapsed)::after {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23e74c3c'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
  transform: rotate(180deg);
}

.accordion-body {
  padding: 20px 25px;
  color: #7f8c8d;
  line-height: 1.6;
  background: #f8f9fa;
  font-size: 1rem;
  border-top: 1px solid #e9ecef;
}

/* Section Titles */
.section__title .sub-title {
  color: #e74c3c;
  font-weight: 600;
  font-size: 1.1rem;
  display: block;
  margin-bottom: 10px;
  text-transform: uppercase;
  letter-spacing: 1px;
}

.section__title .title {
  color: #2c3e50;
  font-size: 2.5rem;
  font-weight: 700;
  margin-bottom: 0;
  line-height: 1.2;
}

/* Loading Animation */
.fa-spin {
  animation: fa-spin 1s infinite linear;
}

@keyframes fa-spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

/* Responsive Design */
@media (max-width: 991px) {
  .contact-info-wrapper {
    position: static;
    margin-top: 50px;
  }
  
  .contact-form-wrapper {
    margin-bottom: 30px;
  }
}

@media (max-width: 768px) {
  .contact-form-wrapper {
    padding: 25px;
  }
  
  .contact-info-card,
  .quick-support-card,
  .social-links-card {
    padding: 20px;
    margin-bottom: 20px;
  }
  
  .support-actions {
    flex-direction: column;
  }
  
  .support-actions .btn {
    width: 100%;
  }
  
  .info-item {
    flex-direction: column;
    text-align: center;
    gap: 10px;
  }
  
  .map-content {
    padding: 0 20px;
  }
  
  .map-content h4 {
    font-size: 1.5rem;
  }
  
  .map-content p {
    font-size: 1rem;
  }
  
  .section__title .title {
    font-size: 2rem;
  }
  
  .accordion-button {
    padding: 15px 20px;
    font-size: 1rem;
  }
  
  .accordion-body {
    padding: 15px 20px;
  }
}

@media (max-width: 480px) {
  .contact-form-wrapper {
    padding: 20px;
  }
  
  .contact-info-card,
  .quick-support-card,
  .social-links-card {
    padding: 15px;
  }
  
  .social-links {
    gap: 8px;
  }
  
  .social-link {
    width: 40px;
    height: 40px;
    font-size: 1rem;
  }
  
  .btn {
    padding: 10px 20px;
    font-size: 0.9rem;
  }
  
  .section__title .title {
    font-size: 1.8rem;
  }
}

/* Focus styles for accessibility */
.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus,
.btn:focus,
.accordion-button:focus,
.social-link:focus {
  outline: 2px solid #e74c3c;
  outline-offset: 2px;
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
  .btn,
  .social-link,
  .info-icon,
  .accordion-button::after,
  .form-group input,
  .form-group select,
  .form-group textarea {
    transition: none;
  }
  
  .btn:hover:not(:disabled),
  .social-link:hover {
    transform: none;
  }
}
</style>