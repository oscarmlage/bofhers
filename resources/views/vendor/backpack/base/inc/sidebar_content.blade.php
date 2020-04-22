<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li><a href="{{ backpack_url('dashboard') }}"><i class="fa fa-dashboard"></i> <span>{{ trans('backpack::base.dashboard') }}</span></a></li>
<li><a href="{{ backpack_url('elfinder') }}"><i class="fa fa-files-o"></i> <span>{{ trans('backpack::crud.file_manager') }}</span></a></li>

<li><a href="{{ backpack_url('software') }}"><i class="fa fa-dashboard"></i> <span>Software</span></a></li>
<li><a href="{{ backpack_url('job') }}"><i class="fa fa-dashboard"></i> <span>Jobs</span></a></li>
<li><a href="{{ backpack_url('category') }}"><i class="fa fa-dashboard"></i> <span>Categories</span></a></li>
<li><a href="{{ backpack_url('bofh') }}"><i class="fa fa-dashboard"></i> <span>BOFHers</span></a></li>
<li><a href='{{ backpack_url('telegram') }}'><i class='fa fa-tag'></i> <span>Telegram</span></a></li>
<li><a href='{{ backpack_url('telegram_canal') }}'><i class='fa fa-tag'></i> <span>Telegram, canales</span></a></li>

<!-- Users, Roles Permissions -->
  <li class="treeview">
    <a href="#"><i class="fa fa-group"></i> <span>Roles, Permissions</span> <i class="fa fa-angle-left pull-right"></i></a>
    <ul class="treeview-menu">
      <li><a href="{{ backpack_url('role') }}"><i class="fa fa-group"></i> <span>Roles</span></a></li>
      <li><a href="{{ backpack_url('permission') }}"><i class="fa fa-key"></i> <span>Permissions</span></a></li>
    </ul>
  </li>
