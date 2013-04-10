{{-- Comments CSS --}}
<link type="text/css" rel="stylesheet" href="{{ Site::css('comments', 'regulus/open-comments') }}" />

{{-- Comments JS --}}
<script type="text/javascript">
	if (baseURL == undefined) var baseURL = "{{ URL::to('') }}";

	var commentLabels   = {{ json_encode(Lang::get('open-comments::labels')) }};
	var commentMessages = {{ json_encode(Lang::get('open-comments::messages')) }};

	@if (!is_null(Site::get('contentID')) && !is_null(Site::get('contentType')))
		var contentID   = "{{ Site::get('contentID') }}";
		var contentType = "{{ Site::get('contentType') }}";
	@else
		if (contentID == undefined)   var contentID   = 0;
		if (contentType == undefined) var contentType = "";
	@endif
</script>

<script type="text/javascript" src="{{ Site::js('wysihtml5', 'regulus/open-comments') }}"></script>
<script type="text/javascript" src="{{ Site::js('wysihtml5-parser-rules', 'regulus/open-comments') }}"></script>

<script type="text/javascript" src="{{ Site::js('comments', 'regulus/open-comments') }}"></script>

{{-- Add Comment Form --}}
@include(Config::get('open-comments::viewsLocation').'partials.add')

{{-- Message --}}
<div id="message-comments">
	<div class="message info hidden"></div>
</div>

{{-- Comments List --}}
<div class="loading" id="loading-comments" title="Loading comments..."></div>
<ul id="comments" class="hidden"></ul>

{{-- JS Template for Comments --}}
@include(Config::get('open-comments::viewsLocation').'templates.list')