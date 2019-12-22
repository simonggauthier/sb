import Vue from 'Vue';

import App from '@components/app.vue';
import Objects from '@api/objects.js';

window.Objects = Objects;

const app = new Vue({
	el: '#app',

	components: {
		App
	}
});
