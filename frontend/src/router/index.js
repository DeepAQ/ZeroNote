import Vue from 'vue'
import Router from 'vue-router'
import NoteEditor from '../components/NoteEditor'

Vue.use(Router)

export default new Router({
  routes: [
    {
      path: '/note/:nbid/:id',
      component: NoteEditor,
      props: true
    }
  ]
})
