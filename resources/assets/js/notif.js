var moment = require('moment');




//
// secondly, require or import Vuetable and optional VuetablePagination component

//
// thirdly, register components to Vue
//
Vue.component('notifications', require('./components/Notifications.vue'));


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */





const app = new Vue({
    el: '#app',
    data: {
        notifications: [],
        count: [],
        userId: id,
        role: role,
    },
    created() {
        this.getNotifications(this.role);
        if(this.role == 'User')
        {
        Echo.private('users.' + this.userId)
        .notification((notification) => {
            this.count++;
            this.notifications.unshift(notification);   
        });
        }
        else if(this.role == 'Admin')
        {
        Echo.private('admin.' + this.userId)
        .notification((notification) => {
            this.count++;
            this.notifications.unshift(notification);  
        });    
        }
        else
        {
        Echo.private('god.' + this.userId)
        .notification((notification) => {
            this.count++;
            this.notifications.unshift(notification);  
        });   
        }
    },
    methods: {
        getNotifications(role) {
            if(role == 'God')
            {
            axios.get('/notifications?role=super').then(response => {
                this.count = response.data.count;
                this.notifications = response.data.notif;
                });
            }
            else
            {axios.get('/notifications').then(response => {
                            this.count = response.data.count;
                            this.notifications = response.data.notif;
                        });
            }
        },
        unreadNotifications() {
            axios.get('/notifications/unread').then(response => {
            });    
            this.count = 0;
        }
    }
});
