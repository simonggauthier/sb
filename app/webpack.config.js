'use strict';

const path = require('path');

const { VueLoaderPlugin } = require('vue-loader')

module.exports = {
	mode: 'development',
	entry: [path.resolve(__dirname, 'src', 'index.js')],
	output: {
		path: path.resolve(__dirname, 'dist'),
		filename: 'bundle.js'
	},
	resolve: {
		alias: {
			'Vue': 'vue/dist/vue.esm.js',
			'components': path.resolve(__dirname, 'src', 'components'),
			'api': path.resolve(__dirname, 'src', 'api'),
			'util': path.resolve(__dirname, 'src', 'util')
		},

		extensions: ['.vue', '.js']
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
