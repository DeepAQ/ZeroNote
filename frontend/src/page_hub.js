import Vue from 'vue'
import Hub from './views/Hub'
import router from './router/hub'
import ElementUI from 'element-ui'
import './styles/theme/index.css'
import './styles/base.less'

Vue.config.productionTip = false

Vue.use(ElementUI)

new Vue({
  el: '#app',
  router,
  template: '<Hub/>',
  components: { Hub }
})
