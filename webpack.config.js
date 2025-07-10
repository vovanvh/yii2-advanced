const path = require('path');

module.exports = {
    entry: './frontend/src/app.ts',
    output: {
        filename: 'bundle.js',
        path: path.resolve(__dirname, 'frontend/web/js'),
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
