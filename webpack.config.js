const path = require('path');

module.exports = {
    entry: {
        backend: './backend/src/app.ts',
        frontend: './frontend/src/app.ts',
    },
    output: {
        filename: '[name]/web/js/bundle.js',
        path: path.resolve(__dirname),
        clean: true
    },
    module: {
        rules: [
            {
                test: /\.ts$/,
                use: 'ts-loader',
                exclude: /node_modules/,
            },
        ],
    },
    resolve: {
        extensions: ['.ts', '.js'],
    },
    mode: 'development',
    devtool: 'source-map'
};
