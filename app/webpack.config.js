module.exports = {
	mode: 'development',
	entry: './src/app.js',
	output: {
		filename: 'bundle.js'
	},
	resolve: {
	    alias: {
	      'Vue': 'vue/dist/vue.esm.js'
	    }
  	}
};
