<template>
  <FrontendLayout>
    <Head :title="t('Learning') + ' - ' + course.name" />
    
    <div class="learning-page">
      <!-- Header -->
      <div class="learning-header">
        <div class="container">
          <div class="header-content">
            <div class="course-info">
              <h1 class="course-title">{{ course.name }}</h1>
              <p class="course-description">{{ course.description }}</p>
              
              <div class="course-meta">
                <div class="meta-item">
                  <i class="fas fa-user-tie"></i>
                  {{ t('Instructor') }}: {{ course.teacher.name }}
                </div>
                <div class="meta-item">
                  <i class="fas fa-chart-line"></i>
                  {{ t('Progress') }}: {{ course.progress }}%
                </div>
                <div class="meta-item">
                  <i class="fas fa-book"></i>
                  {{ t('Subject') }}: {{ course.subject }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="container">
        <div class="learning-layout">
          <!-- Sidebar - Course Content -->
          <div class="learning-sidebar">
            <div class="sidebar-section">
              <h3 class="sidebar-title">{{ t('Course Content') }}</h3>
              
              <div class="content-list">
                <div v-for="(resource, index) in resources" 
                     :key="resource.id" 
                     class="content-item"
                     :class="{ 'active': activeResource === resource.id }"
                     @click="setActiveResource(resource.id)">
                  
                  <div class="item-number">{{ index + 1 }}</div>
                  
                  <div class="item-content">
                    <div class="item-title">{{ resource.title }}</div>
                    <div class="item-meta">
                      <span class="item-type">{{ getResourceType(resource.type) }}</span>
                      <span class="item-duration">{{ resource.duration || '15 min' }}</span>
                    </div>
                  </div>
                  
                  <div class="item-status">
                    <i v-if="isResourceCompleted(resource.id)" 
                       class="fas fa-check-circle completed"></i>
                    <i v-else class="far fa-circle"></i>
                  </div>
                </div>
              </div>
            </div>

            <!-- Progress Section -->
            <div class="progress-section">
              <h4>{{ t('Your Progress') }}</h4>
              <div class="progress-bar-large">
                <div class="progress-fill" :style="{ width: course.progress + '%' }"></div>
              </div>
              <div class="progress-text">{{ course.progress }}% {{ t('Complete') }}</div>
            </div>
          </div>

          <!-- Main Content - Video Player -->
          <div class="learning-main">
            <div class="video-player-section">
              <div class="video-container" v-if="activeResource">
                <div class="video-player">
                  <div v-if="getCurrentResource().is_youtube" class="youtube-player">
                    <iframe 
                      :src="'https://www.youtube.com/embed/' + getCurrentResource().youtube_video_id"
                      frameborder="0"
                      allowfullscreen
                      class="youtube-iframe">
                    </iframe>
                  </div>
                  
                  <div v-else-if="getCurrentResource().file_url" class="video-file-player">
                    <video 
                      controls
                      :poster="getCurrentResource().thumbnail_url"
                      class="video-element">
                      <source :src="getCurrentResource().file_url" type="video/mp4">
                      {{ t('Your browser does not support the video tag.') }}
                    </video>
                  </div>
                  
                  <div v-else class="no-video-available">
                    <i class="fas fa-video-slash"></i>
                    <p>{{ t('No video available for this resource') }}</p>
                  </div>
                </div>
                
                <div class="video-info">
                  <h2 class="video-title">{{ getCurrentResource().title }}</h2>
                  <p class="video-description">{{ getCurrentResource().description }}</p>
                  
                  <div class="video-actions">
                    <button class="btn-primary" @click="markAsCompleted">
                      <i class="fas fa-check"></i>
                      {{ t('Mark as Completed') }}
                    </button>
                    
                    <button class="btn-outline" @click="downloadResource">
                      <i class="fas fa-download"></i>
                      {{ t('Download') }}
                    </button>
                    
                    <button class="btn-outline" @click="toggleNotes">
                      <i class="fas fa-sticky-note"></i>
                      {{ t('Take Notes') }}
                    </button>
                  </div>
                </div>
              </div>
              
              <div v-else class="no-resource-selected">
                <i class="fas fa-play-circle"></i>
                <h3>{{ t('Select a lesson to start learning') }}</h3>
                <p>{{ t('Choose a lesson from the sidebar to begin watching') }}</p>
              </div>
            </div>

            <!-- Notes Section -->
            <div v-if="showNotes" class="notes-section">
              <h3>{{ t('My Notes') }}</h3>
              <textarea 
                v-model="currentNotes"
                :placeholder="t('Write your notes here...')"
                class="notes-textarea">
              </textarea>
              <div class="notes-actions">
                <button class="btn-primary" @click="saveNotes">
                  {{ t('Save Notes') }}
                </button>
                <button class="btn-outline" @click="clearNotes">
                  {{ t('Clear') }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </FrontendLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import FrontendLayout from '../Layout/FrontendLayout.vue'
import { useTranslation } from '@/composables/useTranslation'

const props = defineProps({
  course: {
    type: Object,
    required: true
  },
  resources: {
    type: Array,
    default: () => []
  }
})

const { t } = useTranslation()

const activeResource = ref(props.resources.length > 0 ? props.resources[0].id : null)
const showNotes = ref(false)
const currentNotes = ref('')
const completedResources = ref([])

const getCurrentResource = () => {
  return props.resources.find(r => r.id === activeResource.value) || {}
}

const setActiveResource = (resourceId) => {
  activeResource.value = resourceId
  showNotes.value = false
  // Load notes for this resource
  loadNotes(resourceId)
}

const getResourceType = (type) => {
  const types = {
    'video': t('Video'),
    'document': t('Document'),
    'quiz': t('Quiz'),
    'assignment': t('Assignment')
  }
  return types[type] || type
}

const isResourceCompleted = (resourceId) => {
  return completedResources.value.includes(resourceId)
}

const markAsCompleted = async () => {
  try {
    const response = await fetch(`/api/courses/${props.course.id}/resources/${activeResource.value}/complete`, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Content-Type': 'application/json'
      }
    })
    
    if (response.ok) {
      if (!completedResources.value.includes(activeResource.value)) {
        completedResources.value.push(activeResource.value)
      }
      alert(t('Resource marked as completed!'))
    }
  } catch (error) {
    alert(t('Error marking resource as completed'))
  }
}

const downloadResource = () => {
  const resource = getCurrentResource()
  if (resource.file_url) {
    window.open(resource.file_url, '_blank')
  } else {
    alert(t('No file available for download'))
  }
}

const toggleNotes = () => {
  showNotes.value = !showNotes.value
}

const loadNotes = (resourceId) => {
  // Load saved notes from localStorage or API
  const savedNotes = localStorage.getItem(`notes_${props.course.id}_${resourceId}`)
  currentNotes.value = savedNotes || ''
}

const saveNotes = () => {
  localStorage.setItem(`notes_${props.course.id}_${activeResource.value}`, currentNotes.value)
  alert(t('Notes saved successfully!'))
}

const clearNotes = () => {
  if (confirm(t('Are you sure you want to clear your notes?'))) {
    currentNotes.value = ''
    localStorage.removeItem(`notes_${props.course.id}_${activeResource.value}`)
  }
}
</script>

<style scoped>
.learning-page {
  min-height: 100vh;
  background: #f8f9fa;
}

.learning-header {
  color: black;
  padding: 2rem 0;
}

/* ==================== */
/* BREADCRUMB STYLES */
/* ==================== */
.breadcrumb__area {
  position: relative;
  padding: 10px 0 10px;
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  overflow: hidden;
  color: var(--text-primary);
  background-color: var(--bg-secondary);
}

.breadcrumb__content {
  text-align: center;
  position: relative;
  z-index: 3;
  color: var(--text-primary);
}

.breadcrumb__content .title {
  font-size: 24px;
  font-weight: 400;
  color: var(--text-primary);
  margin-bottom: 15px;
  text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
  transition: color 0.3s ease;
}

.breadcrumb {
  display: flex;
  justify-content: center;
  align-items: center;
  list-style: none;
  padding: 0;
  margin: 0;
  color: var(--text-primary);
  font-size: 16px;
  font-weight: 500;
  transition: color 0.3s ease;
}

.breadcrumb a {
  color: var(--text-primary);
  text-decoration: none;
  opacity: 0.8;
  transition: opacity 0.3s ease, color 0.3s ease;
}

.breadcrumb a:hover {
  opacity: 1;
  color: var(--primary-color);
}

.breadcrumb-separator {
  color: var(--text-muted);
  opacity: 0.8;
  margin: 0 10px;
  font-size: 14px;
  transition: color 0.3s ease;
}

.breadcrumb span:not(.breadcrumb-separator) {
  color: var(--text-primary);
  opacity: 1;
  font-weight: 600;
  transition: color 0.3s ease;
}

/* ==================== */
/* BREADCRUMB SHAPES */
/* ==================== */
.breadcrumb__shape-wrap {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  pointer-events: none;
  overflow: hidden;
  z-index: 1;
}

.breadcrumb__shape-wrap img {
  position: absolute;
  max-width: none;
  opacity: 0.3;
}

.breadcrumb__shape-wrap img:nth-child(1) {
  top: 20%;
  left: 8%;
  width: 120px;
  z-index: 1;
}

.breadcrumb__shape-wrap img:nth-child(2) {
  top: 35%;
  right: 20%;
  width: 80px;
  z-index: 1;
}

.breadcrumb__shape-wrap img:nth-child(3) {
  bottom: 1%;
  left: 32%;
  width: 100px;
  z-index: 1;
}

.breadcrumb__shape-wrap img:nth-child(4) {
  bottom: 2%;
  right: 40%;
  width: 90px;
  z-index: 1;
}

.breadcrumb__shape-wrap img:nth-child(5) {
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 150px;
  z-index: 1;
}

/* Animation for specific elements */
.alltuchtopdown {
  animation: alltuchtopdown 5s infinite linear;
}

@keyframes alltuchtopdown {
  0% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-30px);
  }
  100% {
    transform: translateY(0px);
  }
}
/* ==================== */
/* DARK THEME ENHANCEMENTS */
/* ==================== */
.dark-theme .breadcrumb__area {
  background-color: var(--bg-secondary);
}
.separator {
  color: rgba(255, 255, 255, 0.6);
}

.current {
  color: white;
  font-weight: 500;
}

.course-info h1 {
  margin: 0 0 0.5rem 0;
  font-size: 2rem;
}

.course-description {
  margin: 0 0 1.5rem 0;
  opacity: 0.9;
  font-size: 1.1rem;
}

.course-meta {
  display: flex;
  gap: 2rem;
  flex-wrap: wrap;
}

.meta-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.9rem;
}

.learning-layout {
  display: grid;
  grid-template-columns: 300px 1fr;
  gap: 2rem;
  padding: 2rem 0;
}

.learning-sidebar {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  height: fit-content;
  position: sticky;
  top: 2rem;
}

.sidebar-title {
  margin: 0 0 1rem 0;
  font-size: 1.2rem;
  color: #2d3748;
}

.content-list {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.content-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1rem;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
  border: 1px solid #e2e8f0;
}

.content-item:hover {
  background: #f7fafc;
  border-color: #cbd5e0;
}

.content-item.active {
  background: #ebf8ff;
  border-color: #4299e1;
}

.item-number {
  background: #e2e8f0;
  color: #4a5568;
  width: 2rem;
  height: 2rem;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.9rem;
  font-weight: 600;
}

.content-item.active .item-number {
  background: #4299e1;
  color: white;
}

.item-content {
  flex: 1;
}

.item-title {
  font-weight: 500;
  color: #2d3748;
  margin-bottom: 0.25rem;
}

.item-meta {
  display: flex;
  gap: 1rem;
  font-size: 0.8rem;
  color: #718096;
}

.item-status .completed {
  color: #48bb78;
}

.progress-section {
  margin-top: 2rem;
  padding-top: 1.5rem;
  border-top: 1px solid #e2e8f0;
}

.progress-bar-large {
  background: #e2e8f0;
  height: 8px;
  border-radius: 4px;
  overflow: hidden;
  margin: 0.5rem 0;
}

.progress-fill {
  background: linear-gradient(90deg, #48bb78, #38a169);
  height: 100%;
  transition: width 0.3s ease;
}

.progress-text {
  text-align: center;
  font-weight: 500;
  color: #2d3748;
}

.learning-main {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.video-player-section {
  background: white;
  border-radius: 12px;
  padding: 2rem;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.video-container {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.video-player {
  background: #000;
  border-radius: 8px;
  overflow: hidden;
}

.youtube-iframe, .video-element {
  width: 100%;
  height: 400px;
  border: none;
}

.no-video-available, .no-resource-selected {
  text-align: center;
  padding: 4rem 2rem;
  color: #718096;
}

.no-video-available i, .no-resource-selected i {
  font-size: 4rem;
  margin-bottom: 1rem;
  color: #cbd5e0;
}

.video-info {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
}

.video-title {
  font-size: 1.5rem;
  color: #2d3748;
  margin: 0;
}

.video-description {
  color: #718096;
  line-height: 1.6;
  margin: 0;
}

.video-actions {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

.notes-section {
  background: white;
  border-radius: 12px;
  padding: 2rem;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
  margin-top: 2rem;
}

.notes-textarea {
  width: 100%;
  height: 200px;
  border: 1px solid #e2e8f0;
  border-radius: 8px;
  padding: 1rem;
  font-family: inherit;
  resize: vertical;
  margin: 1rem 0;
}

.notes-actions {
  display: flex;
  gap: 1rem;
}

.btn-primary, .btn-outline {
  padding: 0.75rem 1.5rem;
  border-radius: 8px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.3s ease;
  border: none;
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
}

.btn-primary {
  background: #4299e1;
  color: white;
}

.btn-primary:hover {
  background: #3182ce;
}

.btn-outline {
  background: transparent;
  color: #4299e1;
  border: 1px solid #4299e1;
}

.btn-outline:hover {
  background: #4299e1;
  color: white;
}

@media (max-width: 768px) {
  .learning-layout {
    grid-template-columns: 1fr;
  }
  
  .learning-sidebar {
    position: static;
  }
  
  .course-meta {
    flex-direction: column;
    gap: 1rem;
  }
  
  .video-actions {
    flex-direction: column;
  }
  
  .notes-actions {
    flex-direction: column;
  }
}
</style>