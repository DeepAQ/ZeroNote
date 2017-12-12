import Vue from 'vue'
import Admin from './views/Admin'
import ElementUI from 'element-ui'
import './styles/theme/index.css'
import './styles/base.less'

Vue.config.productionTip = false

Vue.use(ElementUI)

new Vue({
  el: '#app',
  template: '<Admin/>',
  components: { Admin }
})
