// import { createRouter, createWebHistory } from 'vue-router';
// import store from '../store';

// // Auth Components
// import Login from '../Pages/Auth/Login.vue';
// import Registration from '../Pages/Auth/Registration.vue';

// // Student Auth Components
// import StudentLogin from '../Pages/Auth/StudentLogin.vue';
// import StudentRegistration from '../Pages/Auth/StudentRegistration.vue';
// import PhoneVerification from '../Pages/Auth/PhoneVerification.vue';

// // Admin Dashboard Components
// import SuperAdminDashboard from '../Pages/Admin/Dashboard/SuperAdmin.vue';
// import SuperAdmins from '../components/SuperAdmins.vue';
// import Admins from '../Pages/Admin/Users/Admins.vue';
// import OtherUsers from '../Pages/Admin/Dashboard/Teacher.vue'; // This component will now be for Teachers
// import TeacherPortal from '../Pages/Teacher/Portal.vue';

// // Course Management Components
// import AllCourses from '../Pages/Admin/Courses/AllCourses.vue';
// import CourseCategories from '../Pages/Admin/Courses/CourseCategories.vue';
// import Enrollments from '../Pages/Admin/Courses/Enrollments.vue';
// import CourseReviews from '../Pages/Admin/Courses/CourseReviews.vue';
// import ClassSubjects from '../Pages/Admin/Courses/ClassSubjects.vue';
// import SubjectTeachers from '../Pages/Admin/Courses/SubjectTeachers.vue';
// import CategoryClasses from '../Pages/Admin/Courses/CategoryClasses.vue';
// import ClassEnrollments from '../Pages/Admin/Courses/ClassEnrollments.vue';
// import CourseDetails from '../Pages/Admin/Courses/CourseDetails.vue';
// import CourseTeachers from '../Pages/Admin/Courses/CourseTeachers.vue';
// import RegularCourses from '../Pages/Admin/Courses/RegularCourses.vue';
// import SkillCourses from '../Pages/Admin/Courses/SkillCourses.vue';

// // Admission Components
// import Applications from '../Pages/Admin/Admissions/Applications.vue';
// import Approvals from '../Pages/Admin/Admissions/Approvals.vue';
// import EnrolledStudents from '../Pages/Admin/Admissions/EnrolledStudents.vue';

// // Teacher Class Management Components
// import TeacherClassDashboard from '../Pages/Teacher/Class/Dashboard.vue';
// import TeacherClassAssignments from '../Pages/Teacher/Class/Assignments.vue';
// import TeacherClassResources from '../Pages/Teacher/Class/Resources.vue';
// import TeacherClassSchedule from '../Pages/Teacher/Class/Schedule.vue';

// // ============ FRONTEND COMPONENTS ============
// import FrontendLayout from '../Pages/Layout/FrontendLayout.vue';
// import Home from '../Pages/Frontend/Home.vue';
// import Courses from '../Pages/Frontend/Courses.vue';
// import CourseSingle from '../Pages/Frontend/CourseSingle.vue';
// import Instructors from '../Pages/Frontend/Instructors.vue';
// import InstructorDetails from '../Pages/Frontend/InstructorDetails.vue';
// import About from '../Pages/Frontend/About.vue';
// import Contact from '../Pages/Frontend/Contact.vue';
// import Blog from '../Pages/Frontend/Blog.vue';

// const routes = [
//   // ============ FRONTEND ROUTES ============
//   {
//     path: '/',
//     component: FrontendLayout,
//     children: [
//       {
//         path: '',
//         name: 'Home',
//         component: Home,
//         meta: { requiresAuth: false, title: 'SkillGro - Online Learning Platform' }
//       },
//       {
//         path: '/courses',
//         name: 'Courses',
//         component: Courses,
//         meta: { requiresAuth: false, title: 'Courses - SkillGro' }
//       },
//       // In your router.js file, update this route:
//       {
//         path: '/course/:id',  // Changed from :slug to :id
//         name: 'CourseSingle',
//         component: CourseSingle,
//         meta: { requiresAuth: false, title: 'Course Details - SkillGro' }
//       },
//       {
//         path: '/instructors',
//         name: 'Instructors',
//         component: Instructors,
//         meta: { requiresAuth: false, title: 'Our Instructors - SkillGro' }
//       },
//       {
//         path: '/instructor/:id',
//         name: 'InstructorDetails',
//         component: InstructorDetails,
//         props: true
//       },
//       {
//         path: '/about',
//         name: 'About',
//         component: About,
//         meta: { requiresAuth: false, title: 'About Us - SkillGro' }
//       },
//       {
//         path: '/contact',
//         name: 'Contact',
//         component: Contact,
//         meta: { requiresAuth: false, title: 'Contact Us - SkillGro' }
//       },
//       {
//         path: '/blog',
//         name: 'Blog',
//         component: Blog,
//         meta: { requiresAuth: false, title: 'Blog - SkillGro' }
//       }
//     ]
//   },

//   // ============ STUDENT AUTH ROUTES ============
//   {
//     path: '/student-login',
//     name: 'StudentLogin',
//     component: StudentLogin,
//     meta: { requiresAuth: false, title: 'Student Login - SkillGro' }
//   },
//   {
//     path: '/student-registration',
//     name: 'StudentRegistration',
//     component: StudentRegistration,
//     meta: { requiresAuth: false, title: 'Student Registration - SkillGro' }
//   },
//   {
//     path: '/phone-verification',
//     name: 'PhoneVerification',
//     component: PhoneVerification,
//     meta: { requiresAuth: false, title: 'Phone Verification - SkillGro' }
//   },

//   // ============ ADMIN AUTH ROUTES ============
//   {
//     path: '/login',
//     name: 'Login',
//     component: Login,
//     meta: { requiresAuth: false, title: 'Login - SkillGro' }
//   },
//   {
//     path: '/registration',
//     name: 'Registration',
//     component: Registration,
//     meta: { requiresAuth: false, title: 'Register - SkillGro' }
//   },

//   // ============ ADMIN DASHBOARD ROUTES ============
//   {
//     path: '/super-admin',
//     name: 'SuperAdmin',
//     component: SuperAdminDashboard,
//     meta: { requiresAuth: true, role: 'super_admin', title: 'Super Admin Dashboard' }
//   },
//   {
//     path: '/admin',
//     name: 'Admin',
//     component: SuperAdminDashboard,
//     meta: { requiresAuth: true, role: 'admin', title: 'Admin Dashboard' }
//   },
//   // Update this route in your router
//   {
//     path: '/teacher',
//     name: 'Teacher',
//     component: TeacherPortal,
//     meta: { requiresAuth: true, role: 'teacher', title: 'Teacher Dashboard' }
//   },

//   // User Management Routes
//   {
//     path: '/admin/users/super-admins',
//     name: 'SuperAdmins',
//     component: SuperAdmins,
//     meta: { requiresAuth: true, roles: ['super_admin'], title: 'Super Admins' }
//   },
//   {
//     path: '/admin/users/admins',
//     name: 'Admins',
//     component: Admins,
//     meta: { requiresAuth: true, roles: ['super_admin'], title: 'Admins' }
//   },
//   {
//     path: '/admin/users/other-users',
//     name: 'OtherUsers',
//     component: OtherUsers,
//     meta: { requiresAuth: true, roles: ['super_admin', 'admin'], title: 'Teachers' } // Updated title to 'Teachers'
//   },

//   // Teacher Portal Routes
//   {
//     path: '/admin/teacher-portal/:id?',
//     name: 'TeacherPortal',
//     component: TeacherPortal,
//     meta: { requiresAuth: true, roles: ['super_admin', 'admin'], title: 'Teacher Portal' }
//   },

//   // Teacher Class Management Routes
//   {
//     path: '/teacher/class/:classId',
//     name: 'TeacherClassDashboard',
//     component: TeacherClassDashboard,
//     meta: { requiresAuth: true, roles: ['teacher', 'admin', 'super_admin'], title: 'Class Dashboard' }
//   },
//   {
//     path: '/teacher/class/:classId/assignments',
//     name: 'TeacherClassAssignments',
//     component: TeacherClassAssignments,
//     meta: { requiresAuth: true, roles: ['teacher', 'admin', 'super_admin'], title: 'Class Assignments' }
//   },
//   {
//     path: '/teacher/class/:classId/resources',
//     name: 'TeacherClassResources',
//     component: TeacherClassResources,
//     meta: { requiresAuth: true, roles: ['teacher', 'admin', 'super_admin'], title: 'Class Resources' }
//   },
//   {
//     path: '/teacher/class/:classId/schedule',
//     name: 'TeacherClassSchedule',
//     component: TeacherClassSchedule,
//     meta: { requiresAuth: true, roles: ['teacher', 'admin', 'super_admin'], title: 'Class Schedule' }
//   },

//   // Course Management Routes
//   {
//     path: '/admin/courses',
//     redirect: '/admin/courses/all-courses'
//   },
//   {
//     path: '/admin/courses/all-courses',
//     name: 'AllCourses',
//     component: AllCourses,
//     meta: { requiresAuth: true, roles: ['super_admin', 'admin'], title: 'All Courses' }
//   },
//   {
//     path: '/admin/courses/regular-courses',
//     name: 'RegularCourses',
//     component: RegularCourses,
//     meta: { requiresAuth: true, roles: ['super_admin', 'admin'], title: 'Regular Courses' }
//   },
//   {
//     path: '/admin/courses/skill-courses',
//     name: 'SkillCourses',
//     component: SkillCourses,
//     meta: { requiresAuth: true, roles: ['super_admin', 'admin'], title: 'Skill Courses' }
//   },
//   {
//     path: '/admin/courses/categories',
//     name: 'CourseCategories',
//     component: CourseCategories,
//     meta: { requiresAuth: true, roles: ['super_admin', 'admin'], title: 'Course Categories' }
//   },
//   {
//     path: '/admin/courses/enrollments',
//     name: 'Enrollments',
//     component: Enrollments,
//     meta: { requiresAuth: true, roles: ['super_admin', 'admin'], title: 'Enrollments' }
//   },
//   {
//     path: '/admin/courses/reviews',
//     name: 'CourseReviews',
//     component: CourseReviews,
//     meta: { requiresAuth: true, roles: ['super_admin', 'admin'], title: 'Course Reviews' }
//   },
//   {
//     path: '/admin/courses/class/:grade/subjects',
//     name: 'ClassSubjects',
//     component: ClassSubjects,
//     meta: { requiresAuth: true, roles: ['super_admin', 'admin'], title: 'Class Subjects' }
//   },
//   {
//     path: '/admin/courses/class/:grade/subject/:subjectId/teachers',
//     name: 'SubjectTeachers',
//     component: SubjectTeachers,
//     meta: { requiresAuth: true, roles: ['super_admin', 'admin'], title: 'Subject Teachers' }
//   },
//   {
//     path: '/admin/courses/course/:courseId/details',
//     name: 'CourseDetails',
//     component: CourseDetails,
//     meta: { requiresAuth: true, roles: ['super_admin', 'admin'], title: 'Course Details' }
//   },
//   {
//     path: '/admin/courses/course/:courseId/teachers',
//     name: 'CourseTeachers',
//     component: CourseTeachers,
//     meta: { requiresAuth: true, roles: ['super_admin', 'admin'], title: 'Course Teachers' }
//   },
//   {
//     path: '/admin/courses/category/:category',
//     name: 'CategoryClasses',
//     component: CategoryClasses,
//     meta: { requiresAuth: true, roles: ['super_admin', 'admin'], title: 'Category Classes' }
//   },
//   {
//     path: '/admin/courses/enrollments/class/:grade',
//     name: 'ClassEnrollments',
//     component: ClassEnrollments,
//     meta: { requiresAuth: true, roles: ['super_admin', 'admin'], title: 'Class Enrollments' }
//   },

//   // Admission Management Routes
//   {
//     path: '/admin/admissions',
//     redirect: '/admin/admissions/applications'
//   },
//   {
//     path: '/admin/admissions/applications',
//     name: 'Applications',
//     component: Applications,
//     meta: { requiresAuth: true, roles: ['super_admin', 'admin'], title: 'Admission Applications' }
//   },
//   {
//     path: '/admin/admissions/approvals',
//     name: 'Approvals',
//     component: Approvals,
//     meta: { requiresAuth: true, roles: ['super_admin', 'admin'], title: 'Admission Approvals' }
//   },
//   {
//     path: '/admin/admissions/enrolled-students',
//     name: 'EnrolledStudents',
//     component: EnrolledStudents,
//     meta: { requiresAuth: true, roles: ['super_admin', 'admin'], title: 'Enrolled Students' }
//   },

//   // Catch-all route for 404 - Redirect to home instead of login
//   {
//     path: '/:pathMatch(.*)*',
//     redirect: '/'
//   }
// ];

// const router = createRouter({
//   history: createWebHistory(),
//   routes,
//   scrollBehavior(to, from, savedPosition) {
//     if (savedPosition) {
//       return savedPosition;
//     } else {
//       return { top: 0, behavior: 'smooth' };
//     }
//   }
// });

// // Navigation guard
// router.beforeEach((to, from, next) => {
//   // Set page title
//   if (to.meta.title) {
//     document.title = to.meta.title;
//   }

//   const token = localStorage.getItem('token');
//   const user = JSON.parse(localStorage.getItem('user') || '{}');

//   // Frontend routes don't require auth
//   if (to.meta.requiresAuth === false) {
//     next();
//     return;
//   }

//   // Auth required routes
//   if (to.meta.requiresAuth && !token) {
//     // Redirect to admin login for admin routes, student login for student routes
//     if (to.path.startsWith('/admin') || to.path.startsWith('/super-admin') || to.path.startsWith('/teacher')) {
//       next('/login');
//     } else {
//       next('/student-login');
//     }
//   } else if (to.meta.requiresAuth && token) {
//     // Check role permissions
//     const userRole = user.role;
    
//     // Check if route has specific roles requirement
//     if (to.meta.roles && !to.meta.roles.includes(userRole)) {
//       // Redirect to appropriate dashboard based on user role
//       const roleRoutes = {
//         super_admin: '/super-admin',
//         admin: '/admin',
//         teacher: '/teacher',
//         student: '/'
//       };
//       next(roleRoutes[userRole] || '/');
//       return;
//     }
    
//     // Check if route has specific role requirement (single role)
//     if (to.meta.role && userRole !== to.meta.role) {
//       const roleRoutes = {
//         super_admin: '/super-admin',
//         admin: '/admin',
//         teacher: '/teacher',
//         student: '/'
//       };
//       next(roleRoutes[userRole] || '/');
//       return;
//     }
    
//     // Allow access if all checks pass
//     next();
//   } else if (token && (to.path === '/login' || to.path === '/registration' || to.path === '/student-login' || to.path === '/student-registration')) {
//     // Redirect authenticated users away from auth pages
//     const roleRoutes = {
//       super_admin: '/super-admin',
//       admin: '/admin',
//       teacher: '/teacher',
//       student: '/'
//     };
//     next(roleRoutes[user.role] || '/');
//   } else {
//     next();
//   }
// });

// export default router;