import { createRouter, createWebHistory } from 'vue-router';
import Home from '../components/Dashboard.vue';
import Report from '../components/Report.vue';

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
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
