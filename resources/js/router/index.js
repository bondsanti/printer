import { createRouter, createWebHistory } from 'vue-router';
import Home from '../components/Dashboard.vue';
import Report from '../components/Report.vue';
import User from '../components/User.vue';

const routes = [
    {
        path: '/',
        name: 'Home',
        component: Home
    },
    {
        path: '/report',
        name: 'Report',
        component: Report
    },
    {
        path: '/users',
        name: 'User',
        component: User
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
