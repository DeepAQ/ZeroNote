import Vue from 'vue'
import Router from 'vue-router'
import HubIndex from '../components/HubIndex'
import HubRead from '../components/HubRead'
import HubSearch from '../components/HubSearch'

Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/',
      name: 'index',
      component: HubIndex
    },
    {
      path: '/read/:id',
      name: 'read',
      component: HubRead,
      props: true
    },
    {
      path: '/search/:kw',
      name: 'search',
      component: HubSearch,
      props: true
    },
  ]
})
