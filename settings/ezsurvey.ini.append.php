<?php /* #?ini charset="utf-8"?

[RelatedObjectSettings]
ContentObjectClasses[]
ContentObjectClasses[]=


# The extensions where the question types can be found.
[QuestionTypeSettings]
ExtensionDirectories[]
ExtensionDirectories[]=ezsurvey

# Example of custom extension.
# ExtensionDirectories[]=custom

# question types in eZ Survey
[QuestionTypeSettings_ezsurvey]
QuestionTypeList[]
QuestionTypeList[]=sectionheader
QuestionTypeList[]=paragraph
QuestionTypeList[]=multiplechoice
QuestionTypeList[]=textentry
QuestionTypeList[]=numberentry
QuestionTypeList[]=emailentry
QuestionTypeList[]=relatedobject
QuestionTypeList[]=receiver

# Example group of a custom survey attribute in the extension custom
# Will be availlable in:
# <extensiondir>/<extensionname>/modules/survey/classes/ezsurvey<questiontypename>.php
[QuestionTypeSettings_custom]
QuestionTypeList[]


*/ ?>