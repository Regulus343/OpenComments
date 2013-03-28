{{-- Comments CSS --}}
<link type="text/css" rel="stylesheet" href="{{ Site::css('comments', 'regulus/open-comments') }}" />

{{-- Comments JS --}}
<script type="text/javascript">
	if (baseURL == undefined) var baseURL = '{{ URL::to('') }}';

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

{{-- Comments List --}}
<div class="message info hidden" id="message-comments"></li>
<ul id="comments" class="hidden"></ul>