// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import Auth from './Auth'
import ElementUI from 'element-ui'
import VueParticles from 'vue-particles'
import './styles/base.less'
import './styles/theme/index.css'

Vue.config.productionTip = false

Vue.use(ElementUI)
Vue.use(VueParticles)

new Vue({
  el: '#app',
  template: '<Auth/>',
  components: { Auth }
})
