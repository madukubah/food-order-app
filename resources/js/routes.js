import VueRouter from 'vue-router';


let routes = [
    {
        path: '/',
        name: 'landing',
        component: require('./views/landing').default
    }
    ,
    {
        path: '/register',
        name: 'register',
        component: require('./views/register').default,
        meta: {
            auth: false
        }
    },
    {
        path: '/login',
        name: 'login',
        component: require('./views/login').default,
        meta: {
            auth: false
        }
    },
    {
        path: '/admin',
        name: 'admin.index',
        component: require('./views/admin/index').default,
        meta: {
            auth: true
        },
        children: [
            {
                path: 'dashboard',
                name: 'admin.dashboard',
                component: require('./views/account').default
            },
            {
                path: 'account',
                name: 'account',
                component: require('./views/account').default
            },
            {
                path: 'users',
                name: 'admin.users',
                component: require('./views/users').default
            }
            ,
            {
                path: 'menus',
                name: 'admin.menus',
                component: require('./views/admin/menus').default
            }
            ,
            {
                path: 'product',
                name: 'admin.product',
                component: require('./views/admin/menus').default
            }
        ]
    },
];

export default new VueRouter({
    base: '/',
    mode: 'history',
    routes,
    linkActiveClass: 'active'
});
