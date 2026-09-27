# FE-02 最终核对与当前验收（2026-09-27）

**本地核心通过并复用；性能偏差已接受；发布计划定稿；真实目标兼容未验；生产未授权。** 本节覆盖下方历史预检及v1.2中的旧性能决策。当前不再存在性能验收申请，也不执行旧候选环境计划。

## 本轮实际核对

仓库root、origin、main、HEAD均与交接一致，保留全部未提交成果。未找到适用于本工作区的AGENTS.md。读取当前交接、原SEARCH要求、FS矩阵，以及search.php、search-projection.php、search-backfill.php、加载/引导钩子和原始JSON。

修改前对v1.2的239个历史hash逐一核对：236一致；3个差异为DEPENDENCIES.md、STATUS.md、FE-02-production-plan.md，与上一轮preservation.json记载的合法文档修改一致。产品、原始测试证据均匹配。不是继续要求本轮修改后的文档等于旧hash。本轮前后hash及差异见 `output/fe02-final-accepted/`。

原regression.json pass=true、failures=[]，59为顶层记录数（含元字段），不是59个独立断言；overlap九类真实重叠、recovery和fanout均pass=true。fanout尝试25次后正文暂缺，明确25/5重建后31结果、12/12/7分页的原始GET证明恢复，不能用尝试数充当current数。未重新运行应用、浏览器、并发或性能测试；未重新计算P95，仅读取历史报告数值与验证证据字节身份。UI不变，725B/1725B gzip和交互证据沿用。

旧dist父主题缺少4个搜索文件且有12个文件与工作树不同，子主题2个文件不同；Tools ZIP与源文件一致。它们不是当前FE完整发布包；版本号没有提升。详细ZIP条目对比与SHA见release-files及existing-artifacts.json。

## 六项分层结论

| 项目 | 当前结论 | 边界 |
|---|---|---|
| search_local_core | PASS_LOCAL_REUSED | 核心正文/地点/依赖、安全过滤、分页、查询失败、无JS及本地回填证据保留；本轮未发现需修复的新增缺陷 |
| performance_decision | ACCEPTED_DEVIATION_BY_USER | 现有代码、已测环境HTTP P95约671.3ms；500ms数值未达到，历史FAIL_TARGET不改；生产速度未验证 |
| target_correctness | TARGET_COMPATIBILITY_NOT_TESTED | WP_SQLite_DB实测复用；目标SQL/DDL/缓存仅静态分析，未运行验证；阻止直接生产迁移/回填/启用 |
| index_readiness | PLAN_READY / WAITING_AUTH_PRODUCTION | 两张派生表、schema/epoch写入、<=25明确ID及manifest/checkpoint计划已核对；生产未执行 |
| device_and_seo | LOCAL_REUSED / TARGET_SEO_AND_DEVICE_NOT_TESTED | 默认SEO、浏览器/缩放复用；实际插件、真机/辅助技术另列，非已发现功能失败 |
| release_readiness | READY_FOR_REVIEW / WAITING_AUTH_PRODUCTION | 文件边界与回滚计划定稿；缺目标正确性证据及明确生产许可，未上线 |

## 原编号当前态（不新增测试体系）

FS-01～34以[原v1.2矩阵](FE-02-closeout-v1.2.md)为证据基础：FS-09/20/22/25/31沿用后续PASS，不再采用初版PARTIAL。FS-32当前业务决定为ACCEPTED_DEVIATION_BY_USER，历史数值FAIL_TARGET不变。FS-21本地SQLite PASS，目标数据库/缓存单列TARGET_COMPATIBILITY_NOT_TESTED；FS-10/11为DEVICE_NOT_TESTED；FS-29默认栈PASS，FS-30实际SEO待验，FS-34真实设备/目标组合边界保留。其余FS状态原样复用，非本轮重测。

| 原需求 | 当前状态及对应证据 |
|---|---|
| SEARCH-001 | PASS_LOCAL_REUSED，FS-01～04共享入口/响应式 |
| SEARCH-002 | PASS_LOCAL_REUSED，FS-05/06；系统大字/键盘边界见FS-10/11 |
| SEARCH-003 | PASS_LOCAL_REUSED，FS-03/09无JS完整往返后续证据 |
| SEARCH-004 | PASS_LOCAL_REUSED，FS-07/08焦点、菜单；真机键盘待验 |
| SEARCH-005 | PASS_LOCAL_REUSED，FS-09/12/13 GET/往返/分页 |
| SEARCH-006 | PASS_LOCAL_REUSED，FS-15～18公开权限 |
| SEARCH-007 | PASS_LOCAL_REUSED，FS-19/20/21核心正文与失效；生产回填WAITING_AUTH_PRODUCTION |
| SEARCH-008 | PASS_LOCAL_REUSED，FS-21～23排序/计数/分页；目标DB正确性未验 |
| SEARCH-009 | PASS_LOCAL_REUSED，FS-12/14/25/28含真实503证据 |
| SEARCH-010 | PASS_LOCAL_REUSED/PASS_STATIC，FS-24/26输入及隐私 |
| SEARCH-011 | PASS_LOCAL_REUSED，FS-27/28结果页 |
| SEARCH-012 | 默认栈PASS_LOCAL_REUSED，FS-29/30目标SEO未验 |
| SEARCH-013 | FS-31资源/面板PASS复用；FS-32历史FAIL_TARGET、当前ACCEPTED_DEVIATION_BY_USER |
| SEARCH-014 | 本地端到端PASS复用，FS-33/34目标/真机边界单列 |
| SEARCH-015 | 范围保持；本轮已停止，无CMS修改和自动生产动作 |

## 目标已知信息—实现—证据—缺口

| 目标已知信息 | 当前实现及已有证据 | 具体缺项 |
|---|---|---|
| 仅站点solotochina.com与历史aaPanel路径；当前WP/PHP/DB版本未知 | 本地WP6.8.3/PHP WASM8.3.32/WP_SQLite_DB实测 | 目标引擎/驱动/版本/表前缀、SQL模式、存储引擎的脱敏技术信息 |
| 当前生产表/标记未知 | dbDelta两表、stc_search_schema=1；标记已为1直接返回 | 目标DDL/索引/重复初始化/v2保留/标记与表不一致时安全行为 |
| 目标SQL及缓存未知 | INSERT IGNORE…SELECT和带generation/epoch条件UPDATE；九类SQLite重叠及恢复PASS | 目标条件写入、受影响并发、提交前后中断、漏checkpoint、失败重试、0/1/12/13计数分页；实际有持久缓存才验删除/失效 |
| 活动主题/Tools/SEO身份未知 | 本地父0.33.11/子0.13.1/Tools0.26.0及默认SEO证据 | 实际安装hash与SEO准确组合；不能用版本号假定部署字节相同 |

当前可用工具无明确目标技术连接；仓库说明只有历史安装路径及过期部署授权。没有扫描凭据或启动WordPress生产bootstrap，也没有读取文章库存。现有最小技术只读许可仍有效，缺的是可安全使用的连接/脱敏事实；不重复申请同一读取权限。目标未知不安装猜测MySQL。待必要事实可得后，仅执行发布计划中最小正确性验证，无性能工程。

实际副作用：本轮只运行本地hash/JSON/ZIP核对及git diff --check，写入交接文档和本地审计文件；产品代码、UI、合同、应用版本、本地数据库均未改。生产技术读取=否；生产写入/备份=否；commit/push/merge/deploy=否；CMS修改=否；付费调用=否。未创建测试服务、未停止用户或CMS进程、未删除旧目录。

`current_authorized_step=NONE`，`phase_end_stop=true`。完整申请仅见[发布计划](FE-02-production-plan.md)，不另开重复预检。

---

## 以下为历史记录（当前决策以上文为准）

# FE-02 发布准备核查 — 2026-09-27

本轮结论：**本地证据已核验，发布计划可审阅；目标环境未测，生产未授权。** 没有兼容性实测证明需要改动产品代码，因此保留全部 FE-01/FE-02 实现。本报告不替代原 FS-01～34 验收，不把准备完成写成上线完成。

## 身份与本轮验证

- 实际目录：`C:\Users\Mloong\Documents\ChatGPT\solo-to-china`；origin：`https://github.com/Weapon-Tsang/solo-to-china.git`；main；HEAD：`86d6eccc0838fe0a896247a385087b774d7e6736`。未切换、提交或同步仓库。
- 本机 v1.2 报告 SHA-256：`ba4df993ad699e4cd4ac5d94ea9da052d9194e384200559f6cafa56a498c543f`，本次恰与输入报告一致；不是以 HEAD 推定工作树一致。
- `python output/fe02-release-precheck/collect.py`：对 v1.2 `changes.json` 中 **239 个文件 hash 全部匹配**（本轮文档变更前）。包括产品、合同、旧验收与截图。新一轮保全基线、身份、原始 JSON hash、核验结果在 `output/fe02-release-precheck/`；旧目录未删除。
- 复核 `regression.json` 的 pass=true、failures=[]；历史“59”是该对象的顶层记录数，包含 pass/failures 两个元字段，不重新宣称本轮执行了59个独立断言。复核 `overlap.json` 九种场景、请求仍挂起证据及拒绝旧提交；`recovery.json` 与 `fanout.json` pass=true，后者批次为25/5。
- 复核旧30个 HTTP 样本与5个预热，正式样本均200；nearest-rank 重算 P50=657.2281ms、P95=671.2685ms。**不是本轮新测**。旧 SQL P95=8ms 不作为 v3 性能证据。
- 搜索 JS/CSS hash 与历史一致，历史 gzip 725/1725 bytes；本轮未改 UI、CMS 合同、原文或产品源码。沿用已有 Chromium/WebKit、无JS、焦点、错误状态、父/子/Tools组合证据。
- 本轮命令、成本及失败记录见 `../../../output/fe02-release-precheck/commands-and-attempts.md`；精确待审文件列表见 [发布文件清单](FE-02-release-files.md)。未运行应用测试、打包或创建服务。

## 分项状态

| 项目 | 当前结论与证据边界 |
|---|---|
| local_core | PASS_LOCAL_REUSED：239个历史hash匹配，SQLite回归/并发/恢复原证据保留；没有新兼容修复 |
| target_stack_identity | UNKNOWN；仓库部署说明只给 aaPanel/site 路径及发布候选版本，不足以核实当前生产 |
| target_database_and_cache | NOT_TESTED：目标数据库DDL、9类并发、恢复、删除时序、持久对象缓存均未在目标栈执行；已有 WP_SQLite_DB 结果有效 |
| search_http | 历史 Playground FAIL_TARGET（P95 671.3ms >500ms）；目标栈 NOT_TESTED；严格冷缓存 NOT_TESTED；PERF_DECISION_REQUIRED，无偏差授权 |
| index_operations | PLAN_READY / NOT_EXECUTED：见运维计划，首次、日常、版本变更和恢复分别处理 |
| devices_and_seo | 本地浏览器旧证据保留；实际SEO名称/版本未知；真iPhone键盘、系统大字和辅助技术未测 |
| release_plan_ready | READY_FOR_REVIEW，待填目标身份/负责人/时间窗/批准范围，非生产授权 |
| production_activation | NOT_AUTHORIZED；本轮无生产读取、部署、迁移、回填、启用 |

## 环境事实与最小申请

| 字段 | 仓库/历史本地证据 | 当前生产 |
|---|---|---|
| WordPress / PHP | 历史本地6.8.3 / WASM 8.3.32；Playground CLI 3.1.52 | unknown |
| 数据库 / 驱动 / 前缀 | 历史 WP_SQLite_DB / SQLite；使用 `$wpdb->prefix`，不能凭模拟 db_version 判断MySQL | 引擎、精确版本、驱动、前缀均unknown |
| Parent / Child / Tools | 源码0.33.11 / 0.13.1 / 0.26.0，含未提交FE改动；相同版本号不足以标识部署字节 | 当前安装版本及hash unknown |
| SEO / 对象缓存 | 沿用历史默认SEO输出；历史本地没有持久对象缓存 | 名称、版本、drop-in及持久缓存均unknown |

通过仓库README、部署说明、历史记录和 PATH 工具发现核查：未发现已获准目标等效隔离栈；`php/docker/mysql/mariadb/wp` 不在 PATH，node/python存在。这不是全机软件不存在的断言；没有扫描用户profile、秘密配置或登录服务器。

**首选待批准方案（本轮未执行）**：管理员先提供脱敏目标版本清单及现成隔离栈位置；若没有，批准在本机新建便携原生 PHP + MySQL + WordPress 合成测试栈，不安装系统服务或容器守护进程。目标未知时提议 PHP8.3、MySQL8.4、WP6.8.3作为候选，具体补丁与官方下载SHA在准备时固定；这只能得出候选兼容性。若真实目标是MariaDB或其他版本，先匹配目标再测，不把MySQL结论外推。

- 新目录 `output/fe02-native-candidate/`，预算5GiB磁盘、约2GiB运行内存（准备估算非实测），独立数据库 `stc_fe02_candidate`、前缀 `fe02_`、独立媒体；凭据仅新生成测试值。
- 仅绑定127.0.0.1：HTTP9511/9512供独立PHP进程与并发屏障，DB13316；启动前只查占用，若占用改计划，不停止未知进程。后台窗口隐藏；记录自建PID，结束只停这些PID并保留数据。
- 需明确批准：从官方渠道下载/解压运行时与WP、初始化本地测试DB、启动临时进程、安装至少300篇合成攻略及状态/组件样本、移植现有屏障/恢复测试。无公网监听、防火墙变更、CMS连接、生产凭据、云资源或付费服务。
- 未提供真实SEO/缓存制品就保持该两项未测，不随机安装替代插件。确认后复用v1.2应用测试：实际DDL/重复安装/v2保留，缺schema和schema标记存在但表缺失分别测；9类双请求、提交前后中断/丢checkpoint/写失败重试，0/1/12/13/300条分页及原文hash。
- 性能：同300篇和查询语义，5次预热+至少30次完整HTTP，记录所有失败、起止计时边界、代码/数据hash及实际引擎/驱动；nearest-rank。启动时间另列，不以换词代表冷缓存。新证据指出代码瓶颈才最多两轮局部修复复测。

**独立生产只读申请（不包含在本机环境申请中）**：限定 `solotochina.com` 的WordPress运行版本、数据库引擎/驱动/前缀、已装主题/Tools/SEO版本与hash、对象缓存drop-in状态、schema存在性及迁移标记；只返回脱敏字段，不读取wp-config秘密、文章正文、CMS业务库或导出库存。管理员提供这些值也可，不必给登录凭据。

## 真机与SEO后续核查

真实iPhone使用者记录型号、iOS/Safari版本：开搜索→输入/清除→键盘提交→阅读文章→返回保留查询→关闭→检查菜单互斥；系统放大字号后检查按钮、标题、卡片及横向溢出。屏幕阅读器检查搜索名称、焦点进入/返回和状态宣读。模拟WebKit/200%页面字号不充当上述证据。

在匹配生产版本的隔离SEO组合验证：有词/空词/无结果搜索均noindex，普通攻略及栏目不误noindex；robots/canonical无冲突，搜索URL不入sitemap。未知组合继续单列，不影响原有本地证据的保留。

## 收尾

硬性未完成项：目标栈身份及原生DB兼容/并发/恢复、目标性能与500ms决定、实际SEO组合、独立生产权限和可恢复备份/迁移/canary验收。真机待验单列。无新增测试进程需要停止，不触碰既有进程。

`current_authorized_step=NONE`；`phase_end_stop=true`。前端FE-02核心本地开发成果已保留。本轮只完成实际获准的兼容验证与发布准备；剩余环境、性能决定和生产授权逐项列明。已停止，未自动上线或回填，CMS任务不受影响。
