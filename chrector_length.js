jQuery(document).on("keydown","input#keyword",function(e){
            console.log(e.keyCode);
            
                if(jQuery(this).val().length <= 10 || e.keyCode == 8){
                  
                }else{
                    e.preventDefault();    
                }
            
            }); 
