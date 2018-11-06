 
 
 
 function openweb(winname,weburl){

    wx.miniProgram.postMessage({ win: winname, url: weburl });
    // api.execScript({
    //         name: 'root',
    //         script:"openwin('"+winname+"','"+weburl+"');"
    //     });
}
function setlogin(name,value){
    api.execScript({
        name: 'root',
        script:"setlogin('"+name+"','"+value+"');"
    });
}


function closewin(){
    wx.miniProgram.postMessage({ close: 1});
    // api.closeWin();
    // api.setWinAttr({
    //     reload: true
    // });

}

  function setselect(id,url){

        api.execScript({
            name: 'root',
            script:"setSelect('"+id+"','"+url+"');"
        });

  }

