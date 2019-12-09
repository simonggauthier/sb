'use strict';

const { VueLoaderPlugin } = require('vue-loader')

module.exports = {
	mode: 'development',
	entry: './src/index.js',
	output: {
		filename: 'bundle.js'
	},
	resolve: {
	    alias: {
	      'Vue': 'vue/dist/vue.esm.js',
	      '@components': './components'
	    }
  	},
  	module: {
  		rules: [
	  		{
	  			test: /\.vue$/,
	  			loader: 'vue-loader'
	  		},
     		{
				test: /\.js$/,
				loader: 'babel-loader'
		    },
			{
				test: /\.css$/,
				use: [
					'vue-style-loader',
					'css-loader'
				]
			}
  		]
  	},
  	plugins: [
  		new VueLoaderPlugin()
  	]
};
