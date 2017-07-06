<?php
header("Content-Type:text/html;charset=utf-8");
spl_autoload_register(function($className){
    require_once $className.".class.php";
});
$tree = new PdoTrees("mysql:host=localhost;","root","");
if($_POST){
    $pid = $_POST["cates"];
    $name = $_POST["catename"];
    $tree->addCates($name,$pid);
}

?>
<style>
    ul{
        cursor:pointer;
    }
    .active{
        background-color: yellow;
    }
</style>

<form action="PdoTree.php" method="post">
    <input type="hidden" id="val" name="cates">
    父节点:
    <ul id="list">
        <li class="active li" myid="0">请选择（默认一级）</li>
        <?php
            foreach ($tree->getCates() as $val){
                echo "<li class='li' myid=$val[id]>{$val['name']}</li>";
            }
        ?>
    </ul>
    添加类:<input type="text" name="catename"/>
    <input type="submit"/>
</form>
<script type="text/javascript">
    window.onload = function () {
        var lis = document.getElementsByClassName('li');
        var val = document.getElementById("val");
        for(var i=0;i<lis.length;i++){
            lis[i].onclick = function () {
                var id = this.getAttribute("myid");
                var ac = document.getElementsByClassName('active');
                for(var j=0;j<ac.length;j++) {
                    ac[j].classList.remove("active");
                }
                this.classList.add("active");
                val.value = id;
            }
        }
    }

</script>
