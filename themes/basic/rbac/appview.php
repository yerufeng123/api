<?php
    $this->title='权限后台管理系统';
    $this->registerJsFile("@basic/js/plugins/css3-mediaqueries.js",['condition' => 'lt IE9']); 
    $this->registerJsFile("@web/public/basic/js/custom/tables.js"); 
?>
<div class="centercontent tables">
    
    <!-- <div class="pageheader notab">
        <h1 class="pagetitle">Tables</h1>
        <span class="pagedesc">This is a sample description of a page</span>
        
    </div> --><!--pageheader-->
    
    <div id="contentwrapper" class="contentwrapper">   
        <div class="contenttitle2">
            <h3>我管理的应用列表</h3>
        </div><!--contenttitle-->
        <table cellpadding="0" cellspacing="0" border="0" class="stdtable" id="dyntable2">
            <colgroup>
                <col class="con0" style="width: 4%" />
                <col class="con1" />
                <col class="con0" />
                <col class="con1" />
                <col class="con0" />
                <col class="con1" />
            </colgroup>
            <thead>
                <tr>
                  <th class="head0 nosort"><input type="checkbox" /></th>
                    <th class="head0">英文代码</th>
                    <th class="head1">中文名称</th>
                    <th class="head0">管理员</th>
                    <th class="head1">注册时间</th>
                    <th class="head0">权限数</th>
                    <th class="head0">角色数</th>
                    <th class="head0">状态</th>
                    <th class="head0">操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if(isset($data['applist'])):?>
                <?php foreach ($data['applist'] as $row):?>
                <tr class="gradeX">
                  <td align="center"><span class="center">
                    <input type="checkbox" />
                  </span></td>
                    <td><?= $row->name ?></td>
                    <td></td>
                    <td><?= $row->userId ?></td>
                    <td class="center"><?= date('Y-m-d H:i:s',$row->createdAt)?></td>
                    <td class="center"></td>
                    <td class="center"></td>
                    <td class="center"></td>
                    <td class="center"><a href="" class="edit">Edit</a> &nbsp; <a href="" class="delete">Delete</a></td>
                </tr>
            <?php endforeach;?>
                <?php endif; ?>
            </tbody>
        </table>
      <br /><br />
    </div><!--contentwrapper-->
</div><!-- centercontent -->