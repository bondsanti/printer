import { createRouter, createWebHistory } from 'vue-router';
import Home from '../components/Dashboard.vue';
import Chart from '../components/Chart.vue';

const routes = [
    {
        path: '/',
        name: 'Home',
        component: Home
    },
    {
        path: '/chart',
        name: 'Chart',
        component: Chart
    }
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
