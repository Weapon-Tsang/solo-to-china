# 当前执行约束增补（2026-09-27）

本文件下方已有v3接口、断点、日常失效、保留流程继续适用，但其旧权限/性能表述以本节及[唯一发布计划](FE-02-production-plan.md)为准：性能决定已为ACCEPTED_DEVIATION_BY_USER，不需要任何性能复测或300篇新负载。R必须拆分：最小技术只读已获准但缺可用连接；库存/正文/ID清点仍未获准。B/D/S/F/A及commit/push均未授权。

本轮静态核对入口与副作用，未调用安装/manifest/rebuild：`{prefix}stc_search_projection`、`{prefix}stc_search_dependency`；`stc_search_schema='1'`；`_stc_search_epoch_post:<ID>`、`_stc_search_epoch_entity:<key>`、`_stc_search_epoch_capability:tools`。schema标记1时install直接返回；不能当表/索引健康保证。正常保存或manifest→prepare触发install/watch，需DDL/option写入授权。重复调用行为已有代码依据，目标运行安全仍需精确表/标记场景证明。

批准后的具体PHP调用顺序：在授权WP CLI/eval上下文加载 `scripts/search-backfill.php`；以已批准1～25个明确ID调用 `stc_search_backfill_manifest($ids)`，将候选JSON/hash安全保存，再 `stc_search_backfill_apply($manifest, $checkpoint_path)`。这不是可直接执行的独立shell命令；路径、ID、上下文由授权清单确定。manifest生成有副作用，绝不是只读dry-run。checkpoint及manifest均存webroot外。`stc_search_rebuild_batch(after, upper, 25)`是会写数据的有界恢复函数，不是盘点工具。

每批completed/conflicts/failed与ID逐项核对；SKIP_NONPUBLIC位于conflicts，单独解释为已撤回，不算正文完成。恢复重新查generation，有冲突不推进成功；写成功但checkpoint缺失重放同一manifest。只改派生表/options/外部账本，原文/title/excerpt/slug/canonical/发布时间/状态不可改。日常实体/媒体/规则/renderer或备份恢复后，按固定范围用真实有效性谓词检查missing/stale/current，再有界恢复，访客请求不补全库。

原fanout.json已核对：30篇受影响，尝试同步25不等于25 current；显式25/5批完成后新词31结果，12/12/7分页（含依赖文章）。此为历史真实GET证据，本轮未重跑；生产仍须在每批提交之后重新核对current，不能用APPLIED数代替最终有效数。

本轮不清孤立候选、不删旧证据、不增cron/Redis/常驻进程。下方保留期/有界清理仅为待批准策略。恢复旧代码须先证明兼容安全；无独立禁用开关，不编造禁用命令。

---

## 以下为历史记录（当前决策以上文为准）

# FE-02 v3 发布与索引运维计划（待授权，未执行）

本计划补充并优先于旧 production-plan 中的操作描述。产品仍为 `search-projection-3`。运维由站点指定WordPress管理员负责，编辑只负责正常内容更新；不要求编辑逐篇提供ID或逐批批准。执行前填写负责人、环境、时间窗、冻结ID上界和整体manifest hash。当前没有cron或后台补齐保证。

## 实际入口及副作用

| 入口 | 代码行为 |
|---|---|
| functions.php加载inc/search.php，再加载search-projection.php | 注册查询和保存/删除/元数据/实体/目录/Tools钩子；加载本身不直接调用搜索安装，但活动代码立即改变查询行为 |
| save_post → stc_search_post_changed → stc_search_rebuild → stc_search_prepare | 推进post/entity epoch；攻略保存可调用stc_search_install并建表，不能把部署到活动主题称为无DB副作用 |
| stc_search_backfill_manifest → stc_search_prepare | 可建表，stc_search_watch可add_option；只生成候选也**不是只读dry-run** |
| stc_search_install | dbDelta创建前缀projection/dependency两表；更新非autoload的stc_search_schema=1；标记已存在则直接返回，不是逐次健康检查 |
| stc_search_changed / 元数据、删除、目录和active_plugins变化 | 新增/更新非autoload epoch；依赖刷新每请求最多尝试25篇，非保证25篇成功 |
| after_switch_theme / admin_init | 原有页面、分类引导及页面迁移仍存在，可创建缺失页面并更新options；切主题不是纯文件操作 |

两表实际DDL见 `inc/search-projection.php:13`：projection主键post_id，generation/rule_version/capability/input_hash/public_text；dependency主键(generation,resource)，resource索引。旧v2 meta保留，不自动视为有效正文。新查询在SQL计数/分页前校验版本。schema未安装时回退title/excerpt；标记为1但表丢失不是等同安全回退，必须隔离注错核验/修复或恢复经过验证的兼容路径。

代码没有独立“所有批次完成才启用”开关：活动主题加载新查询，每个有效generation发布后立即可检索。要分阶段发布必须在已批准维护窗内控制流量/写入；本轮没有新增开关或执行维护。

## 首次发布的授权与顺序

1. **R：生产只读盘点**独立批准；收集脱敏身份、精确候选ID/固定上界、状态/原文字段hash、当前generation和引用版本，不读取CMS库。只读发现用直接SELECT，不调用prepare、manifest或rebuild。WordPress正常bootstrap可能运行其他钩子，真正只读盘点用受限只读DB连接或管理员导出脱敏清单。
2. **B：备份**独立批准；保存当前代码目录及hash、可恢复的WP数据库/相关meta/options/两张派生表、迁移前不存在的对象清单、配置恢复说明。备份置于站点webroot外受限目录，记录时间、校验和、恢复演练；不把正文/秘密带入仓库报告。
3. 完成目标栈DDL、原子条件、九类并发、恢复、分页、安全过滤测试及性能决定。500ms未达只能经明确批准标ACCEPTED_DEVIATION（环境/时限/测量范围），不能标PASS或豁免隐私/并发。
4. **D：部署**与**S：schema/epoch迁移**须同时覆盖真实副作用；批准流量/编辑维护窗及原有页面引导是否允许。复制包后校验源码hash；显式执行stc_search_install并检查真实表/索引/options/autoload及旧v2数据，而非等首次用户保存触发DDL。避免无必要主题/插件切换；保留上一兼容包。
5. **F：回填**批准整体范围，含候选生成的版本option写入；先最多5篇canary，核对post/previous/new generation、资源版本、原文字段hash、正文命中与内部字段不命中、撤回/密码/依赖失效、计数分页。失败停止，不进入后续批次。随后自动遍历已批准整体manifest，每批<=25，冲突另列。
6. **A：启用/恢复流量**独立批准；活动代码和每次提交已经有部分可搜效果，必须提前约束维护窗。全部核对后才宣传该批准范围正文可搜；缺失/失效/失败/新出现未授权ID列出，不以标题命中代替全文完成。记录发布包hash、DB身份及canary证据。
7. **回滚**停止本次执行器，按批准维护窗恢复已验证兼容代码/查询路径，优先保留原文及新派生数据；旧v2可能含过期数据，不盲回旧body搜索。若回到标题/摘要模式，明确正文能力受限。默认不DROP表、不清epoch、不覆盖原文/全库或删除唯一备份；重新生成投影须另有批准范围。

R/B/D/S/F/A可在一份范围明确的审批中一次批准，但当前全部NOT_AUTHORIZED；历史部署授权不延伸至本次新表和回填。

## 检测、生成批次与核对

管理员在首次上线前、每次内容/实体/媒体批量更新后、Tools启停/renderer或规则版本替换后，以及失败/重启恢复时执行一次有界盘点；日常建议每天一次人工巡检。维护频率是拟议运行约定，不代表已有调度器。新ID超过固定上界留到下一批准范围。

只读发现逻辑：在固定 `ID > cursor AND ID <= upper`、post_type=post、publish、无密码的集合中按ID查询，每页<=25；检查schema真实存在。用当前应用 `stc_search_valid_sql('p')` 生成规则/capability/依赖epoch谓词，对LEFT JOIN的projection分类为missing/current/stale，同时记录post_id、原文字段hash、previous generation、capability与依赖版本。该谓词自身不写DB；不要使用名为rebuild_batch的写入函数来做盘点。读取错误须记录FAILED，不能写成空集合或完成。

整体清单包含上述记录、上界、代码hash、规则、时间及批准范围hash，置于webroot外，权限仅管理员。经F授权后按ID顺序切片（<=25），调用现有 `stc_search_backfill_manifest($ids)`，保存完整候选JSON/hash及每个generation，再调用 `stc_search_backfill_apply($manifest, $checkpoint_path)`。该流程可以一次管理员执行自动遍历全部已批准ID，无需每批再审批；不得发现新ID后无界扩大范围。这里是运行编排约定，尚未新增/实测自动总控程序；现有执行器是被WP CLI/eval上下文加载的PHP函数库，不是独立CLI命令。

每批核对completed/conflicts/failed集合并集等于该批ID且互斥。APPLIED/UNCHANGED只表示提交阶段结果，结束后重新用有效性谓词核对current；期间再被编辑的ID恢复为待处理。SKIP_NONPUBLIC按已撤回记录，不记正文完成。SKIP_CHANGED保留原manifest，重新盘点并生成后续有界计划；不强制覆盖。FAILED/异常/丢checkpoint停止后续批次，修复环境原因后用**相同候选manifest**重放，保留每次ledger副本。执行器每次重建ledger并再次验证generation，不是盲信旧checkpoint；文件锁只保护该路径，跨进程安全依赖数据库条件写。

重复失败不无限重试：批准窗内一次原manifest恢复；仍失败交管理员重新规划。整体结束对比原文字段hash，统计current/missing/stale/nonpublic/conflict/failed，抽查正文与title/excerpt、0/1/12/13和跨页唯一ID；在合成栈核验300条25页，不把生产业务数据当测试fixture。

## 日常失效与保留

内容保存、实体/媒体撤回删除或变更会推进epoch并尝试有限刷新；Tools/renderer/规则变化可能使大量capability失效。后台不存在无限补齐，每请求尝试25不是完成25。管理员巡检发现待处理ID后按同一整体manifest流程修复；批准长期运维范围时可涵盖这些受影响ID，否则先申请新增范围。fanout原例30篇需显式25/5两批后恢复，不推出每次依赖更新必然全体失效。

孤立依赖和失败候选本轮全部保留。拟议生产策略：成功ledger和候选至少保留30天，失败/冲突至少90天且未结案继续保留；实际保留空间须由管理员批准。活动generation、运行中manifest及未知用途记录一律保护。停自建回填执行器后，先在批准固定范围内SELECT列出未被当前projection和保留manifest引用、超过保留期的generation，再每批<=25核对并经单独清理权限删除dependency；两表没有创建时间字段，必须以外部manifest账本时间判断，不能从UUID推断年龄。无账本/未知时间只report，不删除epoch、业务内容、旧测试或唯一备份。
