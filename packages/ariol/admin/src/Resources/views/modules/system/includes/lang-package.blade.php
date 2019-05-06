<div data-lang-package="{{ $code }}" class="panel panel-white">
    <div class="panel-heading">
        <h6 class="panel-title text-semibold d-i">
            {{ $languageName }}
        </h6>
        <div class="localization-filters">
            <label class="checkbox-inline">
                <input id="untranslated" type="checkbox" class="styled">
                {{ translate('system.modules.packageItems.localization.packageItems.untranslated') }}
            </label>
        </div>
        <div class="heading-elements">
            <div class="progress m-l-0">
                <div class="progress-bar progress-bar-{{ $progressBarColor }}" style="width: {{ $translated }}%;">
                    {{ $translated }}%
                </div>
            </div>
        </div>
    </div>
    <ul class="nav nav-lg nav-tabs nav-tabs-bottom nav-tabs-toolbar no-margin">
        @foreach ($package as $key => $tab)
            <li @if ($key == 'system') class="active" @endif>
                <a data-file="{{ $tab['content']['packageTitle'] }}" data-toggle="tab"
                   href="#{{ $key }}" class="legitRipple" aria-expanded="true">
                    {{ !empty($russian[$key]['content']['packageTitle']) ? $russian[$key]['content']['packageTitle'] : $russian[$key]['content']['packageTitle'] }}
                </a>
            </li>
        @endforeach
    </ul>
    <div class="tab-content">
        <form id="save-translate" action="/{{ config('ariol.admin-path') . '/system/localization/save-translate' }}" method="post">
            @foreach ($package as $key => $tab)
                <div class="tab-pane fade{{ $key == 'system' ? ' in active' : null }}" id="{{ $key }}">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-translate">
                            <thead>
                                <tr>
                                    <th class="text-center" style="width: 50%">
                                        @if (count($languagesForTranslation) > 1)
                                            <select id="select-language-for-translation" class="language-select">
                                                @include('ariol::modules.system.includes.list-languages', [
                                                    'selected' => 'ru',
                                                    'listLanguages' => $languagesForTranslation
                                                ])
                                            </select>
                                        @else
                                            Русский
                                        @endif
                                    </th>
                                    <th class="text-center" style="width: 50%">
                                        {{ $languageName }}
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @include('ariol::modules.system.includes.package-words', [
                                    'field' => $key,
                                    'language' => $code,
                                    'package' => $tab['content'],
                                    'words' => $russian[$key]['content']
                                ])
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
            <div class="p-20">
                <button class="btn btn-success">
                    {{ translate('system.form.packageItems.save') }}
                </button>
            </div>
        </form>
    </div>
</div>