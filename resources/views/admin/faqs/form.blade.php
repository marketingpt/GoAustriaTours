<x-admin.input name="question" label="Question" :value="$item->question" required/>
<x-admin.textarea name="answer" label="Answer" :value="$item->answer" rows="5" required hint="Plain text. These appear on the home page and in search-engine FAQ data."/>
<x-admin.checkbox name="is_active" label="Active (shown on the website)" :checked="$item->exists ? $item->is_active : true"/>
