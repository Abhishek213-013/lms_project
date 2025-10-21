import { createRouter, createWebHistory } from 'vue-router';
import store from '../store';

// Auth Components
import Login from '../components/Login.vue';
import Registration from '../components/Registration.vue';

// Student Auth Components
import StudentLogin from '../views/StudentLogin.vue';
import StudentRegistration from '../views/Registration.vue';
import PhoneVerification from '../views/PhoneVerification.vue';

// Admin Dashboard Components
import SuperAdminDashboard from '../components/SuperAdminDashboard.vue';
import SuperAdmins from '../components/SuperAdmins.vue';
import Admins from '../components/Admins.vue';
import OtherUsers from '../components/OtherUsers.vue';
import TeacherPortal from '../components/TeacherPortal.vue';

// Course Management Components
import AllCourses from '../components/courses/AllCourses.vue';
import CourseCategories from '../components/courses/CourseCategories.vue';
import Enrollments from '../components/courses/Enrollments.vue';
import CourseReviews from '../components/courses/CourseReviews.vue';
import ClassSubjects from '../components/courses/ClassSubjects.vue';
import SubjectTeachers from '../components/courses/SubjectTeachers.vue';
import CategoryClasses from '../components/courses/CategoryClasses.vue';
import ClassEnrollments from '../components/courses/ClassEnrollments.vue';
import CourseDetails from '../components/courses/CourseDetails.vue';
import CourseTeachers from '../components/courses/CourseTeachers.vue';
import RegularCourses from '../components/courses/RegularCourses.vue';
import SkillCourses from '../components/courses/SkillCourses.vue';

// Admission Components
import Applications from '../components/Admission/Applications.vue';
import Approvals from '../components/Admission/Approvals.vue';
import EnrolledStudents from '../components/Admission/EnrolledStudents.vue';

// Teacher Class Management Components
import TeacherClassDashboard from '../components/teacher/TeacherClassDashboard.vue';
import TeacherClassAssignments from '../components/teacher/TeacherClassAssignments.vue';
import TeacherClassResources from '../components/teacher/TeacherClassResources.vue';
import TeacherClassSchedule from '../components/teacher/TeacherClassSchedule.vue';

// ============ FRONTEND COMPONENTS ============
import FrontendLayout from '../components/Layout/FrontendLayout.vue';
import Home from '../views/Home.vue';
import Courses from '../views/Courses.vue';
import CourseSingle from '../views/CourseSingle.vue';
import Instructors from '../views/Instructors.vue';
import InstructorDetails from '../views/InstructorDetails.vue';
import About from '../views/About.vue';
import Contact from '../views/Contact.vue';
import Blog from '../views/Blog.vue';

const routes = [
  // ============ FRONTEND ROUTES ============
  {
    path: '/',
    component: FrontendLayout,
    children: [
      {
        path: '',
        name: 'Home',
        component: Home,
        meta: { requiresAuth: false, title: 'SkillGro - Online Learning Platform' }
      },
      {
        path: '/courses',
        name: 'Courses',
        component: Courses,
        meta: { requiresAuth: false, title: 'Courses - SkillGro' }
      },
      // In your router.js file, update this route:
      {
        path: '/course/:id',  // Changed from :slug to :id
        name: 'CourseSingle',
        component: CourseSingle,
        meta: { requiresAuth: false, title: 'Course Details - SkillGro' }
      },
      {
        path: '/instructors',
        name: 'Instructors',
        component: Instructors,
        meta: { requiresAuth: false, title: 'Our Instructors - SkillGro' }
      },
      {
        path: '/instructor/:id',
        name: 'InstructorDetails',
        component: InstructorDetails,
        props: true
      },
      {
        path: '/about',
        name: 'About',
        component: About,
        meta: { requiresAuth: false, title: 'About Us - SkillGro' }
      },
      {
        path: '/contact',
        name: 'Contact',
        component: Contact,
        meta: { requiresAuth: false, title: 'Contact Us - SkillGro' }
      },
      {
        path: '/blog',
        name: 'Blog',
        component: Blog,
        meta: { requiresAuth: false, title: 'Blog - SkillGro' }
      }
    ]
  },

  // ============ STUDENT AUTH ROUTES ============
  {
    path: '/student-login',
    name: 'StudentLogin',
    component: StudentLogin,
    meta: { requiresAuth: false, title: 'Student Login - SkillGro' }
  },
  {
    path: '/student-registration',
    name: 'StudentRegistration',
    component: StudentRegistration,
    meta: { requiresAuth: false, title: 'Student Registration - SkillGro' }
  },
  {
    path: '/phone-verification',
    name: 'PhoneVerification',
    component: PhoneVerification,
    meta: { requiresAuth: false, title: 'Phone Verification - SkillGro' }
  },

  // ============ ADMIN AUTH ROUTES ============
  {
    path: '/login',
    name: 'Login',
    component: Login,
    meta: { requiresAuth: false, title: 'Login - SkillGro' }
  },
  {
    path: '/registration',
    name: 'Registration',
    component: Registration,
    meta: { requiresAuth: false, title: 'Register - SkillGro' }
  },

  // ============ ADMIN DASHBOARD ROUTES ============
  {
    path: '/super-admin',
    name: 'SuperAdmin',
    component: SuperAdminDashboard,
    meta: { requiresAuth: true, role: 'super_admin', title: 'Super Admin Dashboard' }
  },
  {
    path: '/admin',
    name: 'Admin',
    component: SuperAdminDashboard,
    meta: { requiresAuth: true, role: 'admin', title: 'Admin Dashboard' }
  },
  {
    path: '/teacher',
    name: 'Teacher',
    component: SuperAdminDashboard,
    meta: { requiresAuth: true, role: 'teacher', title: 'Teacher Dashboard' }
  },

  // User Management Routes
  {
    path: '/admin/users/super-admins',
    name: 'SuperAdmins',
    component: SuperAdmins,
    meta: { requiresAuth: true, roles: ['super_admin'], title: 'Super Admins' }
  },
  {
    path: '/admin/users/admins',
    name: 'Admins',
    component: Admins,
    meta: { requiresAuth: true, roles: ['super_admin'], title: 'Admins' }
  },
  {
    path: '/admin/users/other-users',
    name: 'OtherUsers',
    component: OtherUsers,
    meta: { requiresAuth: true, roles: ['super_admin', 'admin'], title: 'Other Users' }
  },

  // Teacher Portal Routes
  {
    path: '/admin/teacher-portal/:id?',
    name: 'TeacherPortal',
    component: TeacherPortal,
    meta: { requiresAuth: true, roles: ['super_admin', 'admin'], title: 'Teacher Portal' }
  },

  // Teacher Class Management Routes
  {
    path: '/teacher/class/:classId',
    name: 'TeacherClassDashboard',
    component: TeacherClassDashboard,
    meta: { requiresAuth: true, roles: ['teacher', 'admin', 'super_admin'], title: 'Class Dashboard' }
  },
  {
    path: '/teacher/class/:classId/assignments',
    name: 'TeacherClassAssignments',
    component: TeacherClassAssignments,
    meta: { requiresAuth: true, roles: ['teacher', 'admin', 'super_admin'], title: 'Class Assignments' }
  },
  {
    path: '/teacher/class/:classId/resources',
    name: 'TeacherClassResources',
    component: TeacherClassResources,
    meta: { requiresAuth: true, roles: ['teacher', 'admin', 'super_admin'], title: 'Class Resources' }
  },
  {
    path: '/teacher/class/:classId/schedule',
    name: 'TeacherClassSchedule',
    component: TeacherClassSchedule,
    meta: { requiresAuth: true, roles: ['teacher', 'admin', 'super_admin'], title: 'Class Schedule' }
  },

  // Course Management Routes
  {
    path: '/admin/courses',
    redirect: '/admin/courses/all-courses'
  },
  {
    path: '/admin/courses/all-courses',
    name: 'AllCourses',
    component: AllCourses,
    meta: { requiresAuth: true, roles: ['super_admin', 'admin'], title: 'All Courses' }
  },
  {
    path: '/admin/courses/regular-courses',
    name: 'RegularCourses',
    component: RegularCourses,
    meta: { requiresAuth: true, roles: ['super_admin', 'admin'], title: 'Regular Courses' }
  },
  {
    path: '/admin/courses/skill-courses',
    name: 'SkillCourses',
    component: SkillCourses,
    meta: { requiresAuth: true, roles: ['super_admin', 'admin'], title: 'Skill Courses' }
  },
  {
    path: '/admin/courses/categories',
    name: 'CourseCategories',
    component: CourseCategories,
    meta: { requiresAuth: true, roles: ['super_admin', 'admin'], title: 'Course Categories' }
  },
  {
    path: '/admin/courses/enrollments',
    name: 'Enrollments',
    component: Enrollments,
    meta: { requiresAuth: true, roles: ['super_admin', 'admin'], title: 'Enrollments' }
  },
  {
    path: '/admin/courses/reviews',
    name: 'CourseReviews',
    component: CourseReviews,
    meta: { requiresAuth: true, roles: ['super_admin', 'admin'], title: 'Course Reviews' }
  },
  {
    path: '/admin/courses/class/:grade/subjects',
    name: 'ClassSubjects',
    component: ClassSubjects,
    meta: { requiresAuth: true, roles: ['super_admin', 'admin'], title: 'Class Subjects' }
  },
  {
    path: '/admin/courses/class/:grade/subject/:subjectId/teachers',
    name: 'SubjectTeachers',
    component: SubjectTeachers,
    meta: { requiresAuth: true, roles: ['super_admin', 'admin'], title: 'Subject Teachers' }
  },
  {
    path: '/admin/courses/course/:courseId/details',
    name: 'CourseDetails',
    component: CourseDetails,
    meta: { requiresAuth: true, roles: ['super_admin', 'admin'], title: 'Course Details' }
  },
  {
    path: '/admin/courses/course/:courseId/teachers',
    name: 'CourseTeachers',
    component: CourseTeachers,
    meta: { requiresAuth: true, roles: ['super_admin', 'admin'], title: 'Course Teachers' }
  },
  {
    path: '/admin/courses/category/:category',
    name: 'CategoryClasses',
    component: CategoryClasses,
    meta: { requiresAuth: true, roles: ['super_admin', 'admin'], title: 'Category Classes' }
  },
  {
    path: '/admin/courses/enrollments/class/:grade',
    name: 'ClassEnrollments',
    component: ClassEnrollments,
    meta: { requiresAuth: true, roles: ['super_admin', 'admin'], title: 'Class Enrollments' }
  },

  // Admission Management Routes
  {
    path: '/admin/admissions',
    redirect: '/admin/admissions/applications'
  },
  {
    path: '/admin/admissions/applications',
    name: 'Applications',
    component: Applications,
    meta: { requiresAuth: true, roles: ['super_admin', 'admin'], title: 'Admission Applications' }
  },
  {
    path: '/admin/admissions/approvals',
    name: 'Approvals',
    component: Approvals,
    meta: { requiresAuth: true, roles: ['super_admin', 'admin'], title: 'Admission Approvals' }
  },
  {
    path: '/admin/admissions/enrolled-students',
    name: 'EnrolledStudents',
    component: EnrolledStudents,
    meta: { requiresAuth: true, roles: ['super_admin', 'admin'], title: 'Enrolled Students' }
  },

  // Catch-all route for 404 - Redirect to home instead of login
  {
    path: '/:pathMatch(.*)*',
    redirect: '/'
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior(to, from, savedPosition) {
    if (savedPosition) {
      return savedPosition;
    } else {
      return { top: 0, behavior: 'smooth' };
    }
  }
});

// Navigation guard
router.beforeEach((to, from, next) => {
  // Set page title
  if (to.meta.title) {
    document.title = to.meta.title;
  }

  const token = localStorage.getItem('token');
  const user = JSON.parse(localStorage.getItem('user') || '{}');

  // Frontend routes don't require auth
  if (to.meta.requiresAuth === false) {
    next();
    return;
  }

  // Auth required routes
  if (to.meta.requiresAuth && !token) {
    next('/login');
  } else if (to.meta.requiresAuth && token) {
    // Check role permissions
    if (to.meta.roles && !to.meta.roles.includes(user.role)) {
      const roleRoutes = {
        super_admin: '/super-admin',
        admin: '/admin',
        teacher: '/teacher'
      };
      next(roleRoutes[user.role] || '/');
    } else if (to.meta.role && user.role !== to.meta.role) {
      const roleRoutes = {
        super_admin: '/super-admin',
        admin: '/admin',
        teacher: '/teacher'
      };
      next(roleRoutes[user.role] || '/');
    } else {
      next();
    }
  } else if (token && (to.path === '/login' || to.path === '/registration')) {
    // Redirect authenticated users away from auth pages
    const roleRoutes = {
      super_admin: '/super-admin',
      admin: '/admin',
      teacher: '/teacher'
    };
    next(roleRoutes[user.role] || '/');
  } else {
    next();
  }
});

export default router;