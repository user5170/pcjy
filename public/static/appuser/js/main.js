(function(){

    // 头部固定
    $(window).scroll(function () {
        var _top = $(this).scrollTop();
        // console.log(_top);
        if(_top >= 36){
            $('.header').addClass('fixed');
            // console.log(_top);
        }else{
            $('.header').removeClass('fixed');
        }
    });

    $('#sjTab>div').click(function(){
        var index = $(this).index();
        $(this).addClass('aui-active').siblings('.aui-active').removeClass('aui-active');
        $('.findsj>.selection').eq(index).css('display','block').siblings('.selection').css('display','none');
    });
    $('#guideTab>div').click(function(){
        var index = $(this).index();
        $(this).addClass('aui-active').siblings('.aui-active').removeClass('aui-active');
        $('.guide>.guideDetail').eq(index).css('display','block').siblings('.guideDetail').css('display','none');
    });
    $('.seekc .selNav').click(function(e){
        e.preventDefault(); e.stopPropagation();
        $(this).addClass('current').siblings('.current').removeClass('current');
        $(this).children('.selItems').css('display','block');
        $(this).siblings('.selNav').children('.selItems').css('display','none');
        $('body').css('overflow','hidden');
    });
    $('.seekc .selItems .aui-col-xs-4,.seekc .selItems .aui-col-xs-3').click(function(e){
        // e.preventDefault();
        e.stopPropagation();
        $(this).children().addClass('current');
        $(this).siblings().children('.current').removeClass('current');
        var txt     = $(this).children().text();
        var itemval = $(this).children().attr('value');
        $(this).parents('.selNav').find('.SortItem').val(txt);
        $(this).parents('.selNav').find('.SortItem').data('value',itemval);
        $(this).parents('.selNav').find('.SortItem').trigger('change');
        $('.seekc .selItems').css('display','none');
        $('body').css('overflow','unset');
    });
    $('.seekc .selItems').click(function(e){
        e.preventDefault(); e.stopPropagation();
        clickA(e);
    });
    function clickA(e){
      var target = e.target;
      var x = $('.selItems>.content').clientX;
      var y = $('.selItems>.content').clientY;
      var width = $('.selItems>.content').width();
      var height = $('.selItems>.content').height();
      if(!(target.clientX > x && target.clientX < x+width) || !(target.clientY > y && target.clientY < y+height)){
        $('.seekc .selItems').css('display','none');
        $('body').css('overflow','unset');
      }
    };




})
