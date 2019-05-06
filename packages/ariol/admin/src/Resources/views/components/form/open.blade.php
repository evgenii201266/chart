<form id="{{ $id }}" action="{{ $mainPath }}" enctype="multipart/form-data" method="post"
      class="main-form" {{ (! empty($groupTab)) ? 'data-group-tab=' . $groupTab : null }}>
    {{ csrf_field() }}