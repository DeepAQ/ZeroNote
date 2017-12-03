import Vue from 'vue'
import Auth from './views/Auth'
import ElementUI from 'element-ui'
import VueParticles from 'vue-particles'
import './styles/theme/index.css'
import './styles/base.less'

Vue.config.productionTip = false

Vue.use(ElementUI)
Vue.use(VueParticles)

new Vue({
  el: '#app',
  template: '<Auth/>',
  components: { Auth }
})
