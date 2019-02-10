import Vue from 'vue'
import Router from 'vue-router'
Vue.use(Router);

let routes = [
    {
        path: '/',
        component: () => import('./components/List')
    },
    {
        path: '/AddAlbum',
        component: () => import('./components/AddAlbum')
    }
];

export default new Router({routes})