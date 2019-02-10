import Vue from 'vue';
import ElementUI from 'element-ui';
import 'element-ui/lib/theme-chalk/index.css';
import axios from 'axios';
import routes from './routes'


Vue.config.productionTip = false;
Vue.use(ElementUI);
Vue.prototype.$ajax=axios;

new Vue({
  el: '#app',
  router: routes,
  template: '<router-view/>'
});