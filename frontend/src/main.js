import api from './utils/api'
import Vue from 'vue'
import App from './views/App'
import router from './router'
import ElementUI from 'element-ui'
import mavonEditor from 'mavon-editor'
import 'mavon-editor/dist/css/index.css'
import './styles/theme/index.css'
import './styles/base.less'

Vue.config.productionTip = false

Vue.use(ElementUI)
Vue.use(mavonEditor)

new Vue({
  el: '#app',
  // router,
  template: '<App/>',
  components: { App }
})
