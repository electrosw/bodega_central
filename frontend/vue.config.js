module.exports = {
    devServer: {
      //proxy: 'https://leon.inverluz.cl/cables/public' //PARA DESARROLLO
      proxy: '' //PARA PRODUCCION
    },
    outputDir: '../public',
  
    indexPath: process.env.NODE_ENV === 'production'
      ? '../application/views/home.php'
      : 'index.html',
    
    publicPath: process.env.NODE_ENV === 'production'
      //? '/cables/public/' //PARA DESARROLLO 
      ? 'public/'            //PARA PRODUCCION
      : ''
    
  }
