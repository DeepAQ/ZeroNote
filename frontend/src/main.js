import api from './utils/api'
import Vue from 'vue'
import App from './views/App'
import router from './router'
import ElementUI from 'element-ui'
import './styles/theme/index.css'
import './styles/base.less'

Vue.config.productionTip = false

Vue.use(ElementUI)

new Vue({
  el: '#app',
  // router,
  template: '<App/>',
  components: { App }
})
