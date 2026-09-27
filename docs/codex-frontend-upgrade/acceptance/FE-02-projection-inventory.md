# FE-02 检索投影字段清单（v1.2，2026-09-27）

以下 v1.2 清单为当前实现；末尾 v1.1 清单仅保留历史。完整旧文件另存 `output/fe02-v12/inventory-v11.md`。验收见 [v1.2 报告](FE-02-closeout-v1.2.md)。

| 组件/字段 | 公开显示条件与来源 | 当前投影与依赖 | 更新、撤回和恢复 |
|---|---|---|---|
| post_title / post_excerpt | publish/post/无密码 | 原生字段直接 SQL；不依赖正文投影 | 每次查询读现值；失效正文不阻断有效标题/摘要 |
| paragraph、heading、list、HTML 图注、semantic_group、route_timeline 等已序列化正文 | 现有公开 HTML；先去注释/script/style | 纯文本白名单；文章输入版本 | 文章保存生成；旧版本需明确重建 |
| place_info_card title、description | Tools 地点或公开关联 guide 至少一个存在 | 新增；entity key 的版本与实际 Tools 能力 | 绑定、解绑、公开状态、目录指针/相关记录更新均失效；有界重建 |
| place_info_card 默认 title | 显式 title 缺失时优先 place.name_en，否则 guide.title | 与 renderer 的 `??` 分支一致，不多收集未显示的英文名 | 同上 |
| place_info_card 地名、关联标题 | place.name_zh、city_zh；当前公开 guide.title | 只取该卡实际显示字段；不取整个实体行 | guide 保存、撤回、密码、删除及实体元数据修改推进版本 |
| place_info_card caption | 有 media_id 且 `wp_attachment_is_image` 为真 | 作者存储的 caption；依赖媒体存在性/类型 | 图片删除或类型变更后旧图注不参与查询；不把附件管理标题/摘要当正文 |
| annotated_image title、caption、annotations[].text | 经合同验证且媒体为图片 | 保留原白名单；新增媒体依赖版本 | 保存与回填共用；删除媒体后拒绝旧文字 |
| destination_card 外层 title、description | 合法非空 entity_key；Tools 不可用时仍显示 | 保留 v1.1；支持直接 shortcode、单引号 | 父文章输入、规则；Tools 状态变化后重建可恢复外层 |
| destination_card 内部预填公开正文 | Tools 与 shortcode 可用、place 存在且 name_zh 为 VERIFIED | name_en/name_zh/city_en/city_zh；分别 VERIFIED 的 dropoff、entrance、address、arrival_note 对应文字 | 新增白名单；目录实体版本及 Tools 能力，旧词立即失效，新词在有界重建后可搜 |
| ticket_reminder 外层 title、description | 历史合法 shortcode 参数 | 保留 v1.1 兼容解析；没有恢复产品入口/UI | 文章输入与规则版本；票务计算/日期/库存不纳入 |
| related_guides 自定义 title、显示的关联标题 | 去重、排除自身、仅公开无密码 post，最多12条；有结果才显示 heading | 新增明确编辑文字；直接 post ID/实体绑定版本 | 删除/撤回/密码/解绑后 heading 与旧标题不泄露；不扩展为全站推荐系统 |
| 图片 alt、附件后台标题/摘要、来源证据、实体/媒体内部 ID、URL、schema、状态字段 | 不属于这批 renderer 的可见正文文字 | 不进入 public_text；依赖身份只在内部派生记录 | 隐藏源伪 shortcode 与内部词匿名 GET 为0 |
| planner、affiliate、票务实时计算、价格/库存/优惠及页面壳 | 原非核心排除范围 | 不新增投影 | 不增加商业 UI、模型调用或外部接口 |

媒体的权限判断遵循实际 renderer：当前以图片存在性/类型为条件，不能把普通文章的 draft/private 规则机械套给 attachment。图注来自组件字段；仅编辑附件管理字段不会虚构正文变化。

存储：`{prefix}stc_search_projection` 保存 post_id、generation、规则/能力版本、输入 hash 与白名单 public_text；`{prefix}stc_search_dependency` 保存 generation/resource/version。资源 epoch 是非 autoload 的 option，实体/媒体身份可反查。只有当前 generation 的关系用于反向刷新，因此一篇移除引用后不继续被该依赖刷新；中断候选不会扩大有效反向集合。

查询不接受旧 `search-projection-2` 或能力不匹配的投影。相关更新立即推进版本，SQL 在计数/分页前拒绝失效正文；同步刷新上限25，其余通过固定上界/ID游标或显式至多25项 manifest 重建。不是“等待未来 cron”来保护失效窗口。失败候选保留为非公开孤立依赖，生产清理策略须单独确定。

实际证据：`output/fe02-v12/regression.json`（59项）、`fanout.json`（30篇共享依赖/有界重建）、`combos.json`（父/子/Tools/解绑）、`overlap.json`（双worker）、`recovery.json`、`performance.json`。未读取生产数据，覆盖数量和生产 ID 范围仍未知。

## 以下为 v1.1 历史清单（已被上文替代）

依据真实 Registry、`inc/cms-articles.php` serializer、`inc/content-renderers.php`、`inc/experience-components.php`、Tools `includes/places.php` 与 `includes/catalog.php`。本表没有修改 CMS 合同或扩大实时数据范围。

| 类型 / 存储 | 读者正文文字 | 当前检索 / 来源 | 更新与失效 | 判定 |
|---|---|---|---|---|
| WordPress 标题、摘要 | post_title、post_excerpt | 主查询直接检索 | 下一请求读现值；强制 publish/post/无密码 | 已验证 |
| paragraph / heading / list / image | 正文、标题、列表项、图注 | 已序列化 post_content HTML，保存时生成纯文本 | save_post_post；不公开时删除两项搜索 meta | 已支持；图片 alt 为属性，不作为可见正文纳入 |
| quick_answer / tip / warning | title、answer/content | semantic_group 内 HTML | 同上 | 已支持 |
| key_takeaways / steps / checklist | title、items、截图 caption | semantic_group 内 HTML | 同上 | 已支持 |
| quick_facts | title、items.label/value | semantic_group 内 HTML | 同上 | 已支持 |
| route_timeline | title、items.title/detail/transport_mode/travel_time/note | semantic_group 内 HTML | 同上 | 六个仅字段内的独特词真实 GET 均命中 |
| comparison_table / pros_cons / faq | caption、列名/单元格、优缺点标题与条目、问题/答案 | semantic_group 内 HTML | 同上 | 已支持，不因结构化输入而排除 |
| annotated_image | title、caption、annotations[].text | 验证过的 shortcode payload 白名单；保存时检查 media_id 为图片 | 本文章保存会重建；**图片删除/变更缺少反向依赖失效** | 三个独特词命中；依赖失效仍有缺口，不能对所有状态宣称 PASS |
| destination_card / ticket_reminder | 外层 title、description | **本轮新增**：验证后的 payload；兼容历史直接 shortcode 参数 | 同文章保存/公开状态变化；即使 Tools 禁用，renderer 仍显示这些外层文字 | 四字段此前可见却 0 命中，修改后各 1 命中；内部工具数据未纳入 |
| place_info_card | title、description、有效图片 caption；地点中英文名称、关联文章标题 | **未投影**；卡片依赖 Tools 地点或公开关联文章存在 | renderer 可由 catalog 活动版本、插件状态、关联文章状态/实体元数据改变；当前没有搜索反向依赖与原子失效协议 | **LOCAL_FUNCTION_FAILED / PARTIAL_LOCAL**：测试 title/description 可见但 0 命中；属于核心正文漏搜，不是商业排除项 |
| related_guides | 自定义栏目 title、关联文章标题 | 未投影 | 依赖目标文章当前公开状态、标题、实体解析；无反向依赖 | 关联推荐列表未计入本期全文正文；自定义编辑文字若作为核心正文使用，仍须按 FS-20 缺口处理 |
| destination/ticket 内部运行结果 | 地名、地址、到达说明、票务计算结果 | 未投影 | 版本化地点目录或运行时状态，非文章自身快照 | 与外层说明分开；票务计算/实时状态 OUT_OF_SCOPE_DECLARED；正文性到达说明不能宣称已覆盖 |
| planner / affiliate 各卡片 | 营销、价格、优惠、广告、CTA | 不投影 | 商业资产状态/提供方可变化 | OUT_OF_SCOPE_DECLARED，非核心动态内容 |
| 模板 Hero、Share、TOC、breadcrumb、guide card/list | 页面壳、导航、重复文章标题 | 不另建投影 | 模板/原文章负责 | 无正文新增来源 |

所有类型均不纳入原始 payload、shortcode 名称、entity_key/asset_id/media_id、来源证据、后台状态、私有 URL、未发布文本。查询阶段没有全站 shortcode 渲染、外部服务调用或全量 postmeta 拼接。

本轮只修改父主题 `inc/search.php`：通过 WordPress shortcode parser 读取白名单，新增两个组件的外层字段，fingerprint 加入 `search-projection-2`。没有改写 post_content、标题、摘要或 canonical；查询 SQL/排序/12 条分页未改变。历史已保存文章的投影需要重建才包含新增字段。

关联实体缺口的最小续修范围：在前端中为依赖建立精确反向关系，跟踪 catalog 活动修订、插件可用性、关联文章公开状态/实体元数据及媒体存在性；只重建受影响的文章，并验证撤回/删图/目录切换后的下一请求不再命中过期文字。当前版本无法可靠证明这一点，因此本轮没有把这些字段冒险并入索引，也没有把整个动态正文标为通过。

证据：`../../../output/fe02-closeout/before.json`、`after.json`、`state.json`、`combos-http.json`，以及原 FE-02 静态正文与 annotation 证据。FS-20 当前整体仍为 PARTIAL_LOCAL。
