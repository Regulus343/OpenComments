{{-- Comments CSS --}}
<link type="text/css" rel="stylesheet" href="{{ Site::css('comments', 'regulus/open-comments') }}" />

{{-- Comments JS --}}
<script type="text/javascript">
	if (baseURL == undefined) var baseURL = '{{ URL::to('') }}';
</script>

<script type="text/javascript" src="{{ Site::js('wysihtml5', 'regulus/open-comments') }}"></script>
<script type="text/javascript" src="{{ Site::js('wysihtml5-parser-rules', 'regulus/open-comments') }}"></script>

<script type="text/javascript" src="{{ Site::js('comments', 'regulus/open-comments') }}"></script>

{{-- Add Comment Form --}}
@include(Config::get('open-comments::viewsLocation').'partials.add')

{{-- Comments List --}}
@include(Config::get('open-comments::viewsLocation').'templates.list')